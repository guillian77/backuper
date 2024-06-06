export default class Alert {
    ALERT_TYPE_SUCCESS = "success"
    ALERT_TYPE_INFO    = "info";
    ALERT_TYPE_WARNING = "warning";
    ALERT_TYPE_ERROR   = "error";

    alertContainerElement

    constructor() {
        this.alertContainerElement = document.querySelector("#alert-container")
    }

    success(message) { this.#createAlertElement(this.ALERT_TYPE_SUCCESS, message) }
    info(message) { this.#createAlertElement(this.ALERT_TYPE_INFO, message) }
    error(message) { this.#createAlertElement(this.ALERT_TYPE_ERROR, message) }
    warning(message) { this.#createAlertElement(this.ALERT_TYPE_WARNING, message) }

    #createAlertElement(type, message) {
        let alertElement = document.createElement("p")
            alertElement.classList.add("alert-message", "container", type)
            alertElement.innerText = message

        this.alertContainerElement.append(alertElement)
    }
}
