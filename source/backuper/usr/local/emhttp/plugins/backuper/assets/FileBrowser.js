import Xhr from "./Xhr.js";

/**
 * Display a file browser on input.
 */
export default class FileBrowser {
    /**
     * Input HTMLElement who trigger browser list.
     *
     * @type {HTMLElement}
     */
    input = null

    parent

    /**
     * Container HTMLElement that contain list of directories.
     *
     * @type {HTMLElement}
     */
    container = null

    constructor(input) {
        this.input = input
        this.parent = input.parentElement
        this.container = this.input.parentNode.lastElementChild

        input.addEventListener("input", (e) => this.#handleBrowser(e.target.value))
        input.addEventListener("click", (e) => this.#handleBrowser(e.target.value))

        this.parent.addEventListener("mouseleave", _ => this.#clearContainer(true) )
        this.parent.addEventListener("keydown", e => {
            if (e.code !== "Escape") { return }
            this.#clearContainer(true)
        })
    }

    /**
     * Get directories from a path.
     *
     * @param {String} path Reference path to get list of directories.
     * @param {Boolean} withPrevious Path of previous value in input (used when hit "..").
     *
     * @return {Promise} Directories list.
     */
    async #getDirs(path, withPrevious = false) {
        return Xhr.post("BrowserController", "browseAction", {
            target: path,
        })
    }

    async #handleBrowser(typedValue, withPrevious = false) {
        let dirs = JSON.parse(await this.#getDirs(typedValue, withPrevious))
        let previous = dirs['parent']

        if (!dirs) { return }

        this.#clearContainer()
        this.#displayContainer()

        for (let key in dirs) {
            let dir = dirs[key]

            if (key === "parent") { break; }

            let row = this.#createRowElement(dir)

            this.container.append(row)

            row.addEventListener("click", e => {
                let clickValue = e.target.innerText

                if (clickValue === "..") { clickValue = previous }

                this.#fillInputWith(clickValue)
                this.#handleBrowser(clickValue)
            })
        }
    }

    /**
     * Create a row element.
     *
     * @param dir
     *
     * @return {HTMLLIElement}
     */
    #createRowElement(dir) {
        let row = document.createElement("li")
            row.innerText = dir

        return row
    }

    #clearContainer(hide = false) {
        this.container.innerHTML = ""

        if (!hide) { return }

        this.container.style.display = "none"
    }

    #displayContainer() { this.container.style.display = "block" }

    #fillInputWith(value) { this.input.value = value }
}
