import Backuper from "./Backuper.js";
import TemplateExtension from "./TemplateExtension.js";

new Backuper(
    TemplateExtension.variableFromTemplate("dirs"),
    TemplateExtension.variableFromTemplate("conf")
)
