document.getElementById('backup_db').addEventListener('click', function() {
    var xhr = new XMLHttpRequest();
    var backupStatus = document.getElementById('backup_status');

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var a = document.createElement('a');
            a.href = window.URL.createObjectURL(xhr.response);
            a.download = 'db_backup.zip';
            a.style.display = 'none';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            backupStatus.textContent = '';
        }
    };

    xhr.open('POST', dbBackupZipAjax.ajax_url, true);
    xhr.responseType = 'blob';
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send('action=db_backup_zip_plugin_download&_ajax_nonce=' + dbBackupZipAjax.nonce);

    backupStatus.textContent = 'Creando copia de seguridad...';
});
