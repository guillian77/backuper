export default class Backuper {
    toDeleteIds = []

    constructor(dirs, conf) {
        for (let dir of dirs) {
            this.#addToDirList(dir.type, dir.path, dir.id)
        }

        this.#handleConfiguration(conf)
        this.#initAddButtons()
        this.#handleForm()

        return this
    }

    #handleConfiguration(configurations) {
        for (let conf of configurations) {
            if (!conf.key) { continue }
            if (!conf.value) { continue }

            this.#applyConfiguration(conf)
        }
    }

    #applyConfiguration(conf) {
        let element = document.querySelector(`.backuper_conf[name='conf[${conf.key}]']`)

        if (!element) { throw new Error(`${conf.key} element not found.`) }

        if (element.type === "checkbox") {
            if (conf.value === "0") { return; }

            element.checked = conf.value

            return
        }

        element.value = conf.value
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

    #addToDirList(type, value, id) {
        document
            .querySelector(`#${type}_dir_list`)
            .append(this.#createPathInput(type, value, id))
    }

    #createPathInput(type, value, id) {
        if (!["backup", "target"].includes(type)) { throw new Error(`${type} not allowed.`) }
        if (!id ) { id = "" }
        if (!value ) { value = "" }

        let input = document.createElement("input")
            input.type = "text"
            input.name = `${type}_dir[${id}]`
            input.placeholder = "/mnt/user/directory_to_save"
            input.title = "Add your path"
            input.classList.add("input-row")
            input.value = value

        let removeButton = document.createElement("span")
            removeButton.classList.add("icon-u-delete", "btn", "remove")
            removeButton.setAttribute('data-id', id)

        removeButton.addEventListener("click", e => { this.#deleteDir(e.target) })

        let container = document.createElement("div")
            container.classList.add("input-list-container")
            container.append(input)
            container.append(removeButton)
            container.dataset['type'] = type

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
