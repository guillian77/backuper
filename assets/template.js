export default class Template {
    static ALLOWED_INPUT_TYPES = ["backup", "target"]

    static getTemplateVariable(key) {
        let hiddenElement = document.querySelector(`input[type=hidden]#${key}`)

        if (hiddenElement === undefined) {
            console.error(`${key} not found.`)
        }

        return JSON.parse(hiddenElement.getAttribute(`data-${key}`))
    }

    static createPathInput(type, value, id) {
        if (!this.ALLOWED_INPUT_TYPES.includes(type)) {
            throw new Error(`${type} not allowed.`)
        }

        let input = document.createElement("input")

        input.type = "text"
        input.name = `${type}_dir[${id}]`
        input.placeholder = "/mnt/user/directory_to_save"
        input.title = "Add your path"
        input.classList.add("input-row")
        input.value = value

        return input
    }
}
