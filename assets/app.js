import Template from "./template.js";

let backupDirs = Template.getTemplateVariable("backup_dirs")
let target_dirs = Template.getTemplateVariable("target_dirs")

function appendList(type, dirs) {
    let listContainer = document.querySelector(`#${type}_dir_list`)

    if (!dirs) { return }

    for (let dir of dirs) {
        listContainer.append(Template.createPathInput(type, dir.path, dir.id))
    }
}

appendList("target", target_dirs)
appendList("backup", backupDirs)
