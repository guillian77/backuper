import Xhr from "./Xhr.js";

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

    previous = null

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
     * @param {String} prev Path of previous value in input (used when hit "..").
     *
     * @return {Promise} Directories list.
     */
    async #getDirs(path, prev = null) {
        return Xhr.post("BrowserController", "browseAction", {
            target: path,
            previous: prev
        })
    }

    async #handleBrowser(typedValue) {
        let dirs = JSON.parse(await this.#getDirs(typedValue, this.previous))

        if (!dirs) { return }

        this.#clearContainer()

        dirs.forEach(dir => {
            let row = this.#createRowElement(dir)

            this.container.append(row)
            this.container.style.display = "block"

            row.addEventListener("click", clickEvent => {
                if (clickEvent.target.innerText === "..") {
                    //Do something.
                }

                this.input.value = clickEvent.target.innerText

                this.#clearContainer()
            })
        })
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
}
