import Alert from "./Alert.js";

export default class Backuper {
    alert = new Alert()

    toDeleteIds = []

    conf = null

    constructor(dirs, conf) {
        this.conf = conf

        for (let dir of dirs) {
            this.#addToDirList(dir.type, dir.path, dir.id, dir.paused)
        }

        Object.keys(conf).map(confKey => { this.#applyConfiguration(confKey) })
        this.#initAddButtons()
        this.#handleForm()

        return this
    }

    #applyConfiguration(confName) {
        if (confName === "id") { return; }

        let element = document.querySelector(`.backuper_conf[name='conf[${confName}]']`)

        if (!element) { this.alert.warning(`Unable to find ${confName} in form.`); return; }

        if (element.type === "checkbox") {
            if (this.conf[confName] === "0") { return; }

            element.checked = this.conf[confName]

            return
        }

        element.value = this.conf[confName]
    }

    #initAddButtons() {
        document.querySelectorAll(".btn.add").forEach(addButton => {
            addButton.addEventListener("click", e => {
                this.#addToDirList(e.target.dataset['type'])
            })
        })
    }

    #handleForm() {
        let form = document.querySelector("form#backuper-form")

        form.addEventListener("submit", () => {
            document
                .querySelector("input#deleted_dirs")
                .value = JSON.stringify(this.toDeleteIds)
        })
    }

    #addToDirList(type, value, id, paused = false) {
        document
            .querySelector(`#${type}_dir_list button`)
            .before(this.#createPathInput(type, value, id, paused))
    }

    #createPathInput(type, value, id, paused = false) {
        let random = Math.floor(Math.random() * (new Date()).getTime())
        if (!["backup", "target"].includes(type)) { throw new Error(`${type} not allowed.`) }
        if (!id ) { id = `new-${random}` }
        if (!value ) { value = "" }

        let input = document.createElement("input")
            input.type = "text"
            input.name = `${type}_dir[${id}]`
            input.placeholder = "/mnt/user/directory_to_save"
            input.autocomplete = "off"
            input.title = "Add your path"
            input.classList.add("input-row")
            input.value = value

        let removeButton = document.createElement("span")
            removeButton.classList.add("icon-u-delete", "btn", "remove")
            removeButton.setAttribute('data-id', id)
            removeButton.title = `Remove this ${type} directory.`

        let visualizeIcon = document.createElement("a")
            visualizeIcon.classList.add("icon-folder", "btn")
            visualizeIcon.href = "Main/Browse?dir=" + encodeURIComponent(value)
            visualizeIcon.target = "_blank"
            visualizeIcon.title = "Explore target directory."

        let pauseClass = (paused) ? "play" : "pause"
        let pauseIcon = document.createElement("span")
            pauseIcon.setAttribute('data-id', id)
            pauseIcon.classList.add(`icon-u-${pauseClass}`, "pause-handler", "btn")
            pauseIcon.title = `Set ${pauseClass} for this directory.`
            pauseIcon.dataset.pause = paused.toString()

        let browserList = document.createElement("ul")
            browserList.classList.add("browser-list")

        removeButton.addEventListener("click", e => { this.#deleteDir(e.target) })

        let container = document.createElement("div")
            container.classList.add("input-list-container")
            container.dataset['type'] = type
            container.append(input)
            container.append(removeButton)
            if (type === "target") container.append(visualizeIcon)
            if (type === "backup") container.append(pauseIcon)
            container.append(browserList)

        return container
    }

    /**
     * Delete a directory from a list.
     *
     * Also update the IDs list.
     *
      * @param dirElement
     */
    #deleteDir(dirElement) {
         this.#addToDeleteList(dirElement.dataset['id'])

         dirElement.parentElement.remove()
    }

    #addToDeleteList(id) {
        let updatedDeleteList = [...this.toDeleteIds, id]

        this.toDeleteIds = [...new Set(updatedDeleteList)]

        return this
    }
}
