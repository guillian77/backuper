import Backuper from "./Backuper.js";
import TemplateExtension from "./TemplateExtension.js";
import Xhr from "./Xhr.js";

new Backuper(
    TemplateExtension.variableFromTemplate("dirs"),
    TemplateExtension.variableFromTemplate("conf")
)

/**
 * --------------------------------------------------
 * Toggle paused directories.
 * --------------------------------------------------
 */
function pauseDirectory(element) {
    let isPaused = (element.dataset.pause === "true")
    let toggledValue = !isPaused
    let classes = { true: "icon-u-play", false: "icon-u-pause" }
    let targetClass = classes[toggledValue]

    // Toggle pause.
    element.dataset.pause = toggledValue.toString()

    // Clear existing classes.
    element.classList.remove("icon-u-pause", "icon-u-play")

    // Add the correct class.
    element.classList.add(targetClass)

    Xhr
        .post("DirectoryController", "pauseAction", {
            pause: toggledValue,
            id: element.dataset.id,
        })
}

let pauseElements = document.querySelectorAll("span.pause-handler")
pauseElements.forEach((el) => {
    el.addEventListener("click", e => pauseDirectory(e.target))
})

/**
 * --------------------------------------------------
 * Toggle hidden encrypt section
 * --------------------------------------------------
 */
let encryptCheckbox = document.querySelector("input[name='conf[encrypt_enabled]']")
let encryptionSection = document.querySelector("#encrypt-section")

if (encryptCheckbox.checked) { encryptionSection.style.display = "block" }
encryptCheckbox.addEventListener("change", e => {
    encryptionSection.style.display = "none"

    if (e.target.checked) { encryptionSection.style.display = "block" }
})

/**
 * --------------------------------------------------
 * Toggle hidden purge section
 * --------------------------------------------------
 */
let purgeCheckbox = document.querySelector("input[name='conf[purge_enabled]']")
let purgeSection = document.querySelector("#purge-section")

if (purgeCheckbox.checked) { purgeSection.style.display = "block" }
purgeCheckbox.addEventListener("change", e => {
    purgeSection.style.display = "none"

    if (e.target.checked) { purgeSection.style.display = "block" }
})
