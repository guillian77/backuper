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
    // Toggle pause.
    element.dataset.pause = (element.dataset.pause === "true") ? "false" : "true"

    Xhr
        .post("DirectoryController", "pauseAction", {
            pause: (element.dataset.pause === "true"),
            id: element.dataset.id,
        })
}

let pauseElements = document.querySelectorAll("span.icon-u-pause")
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
