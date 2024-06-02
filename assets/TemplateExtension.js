export default class TemplateExtension {
    static variableFromTemplate(key) {
        let hiddenElement = document.querySelector(`input[type=hidden]#${key}`)

        if (hiddenElement === undefined) {
            console.error(`${key} not found.`)
        }

        return JSON.parse(hiddenElement.getAttribute(`data-${key}`))
    }
}
