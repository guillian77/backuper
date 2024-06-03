export default class TemplateExtension {
    static variableFromTemplate(key) {
        let hiddenElement = document.querySelector(`input[type=hidden]#${key}`)

        if (!hiddenElement) { console.error(`${key} not found.`) }

        return JSON.parse(hiddenElement.getAttribute(`data-${key}`))
    }
}
