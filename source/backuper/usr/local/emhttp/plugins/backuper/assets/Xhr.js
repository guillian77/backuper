export default class Xhr {
    static post(controller, method, data) {
        return $.post('/plugins/backuper/xhr.php', {
            controller: controller,
            method: method,
            data: data,
        });
    }
}
