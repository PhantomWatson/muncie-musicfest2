var mediaUpload = {
    init: function (params) {
        var bandId = $('#bandId').val();
        
        $(params.buttonSelector).uploadifive({
            buttonText: params.buttonText,
            checkScript: false,
            fileSizeLimit: params.fileSizeLimit+'B',
            fileType: params.fileType,
            formData: {
                timestamp: params.timestamp,
                token: params.token,
                bandId: bandId
            },
            uploadScript: params.uploadScript,
            width: 250,
            
            onCheck: false,
            onUpload: function (filesToUpload) {
                
            },
            'onUploadComplete': function(file, data) {
                //data.trackName
            },
            'onFallback': function() {
                // Warn user that their browser is old
            },
            'onError': function(errorType, files) {
                var response = $.parseJSON(files.xhr.responseText);
                alert('There was an error uploading '+files.name+' ('+response.message+')');
            },
            'onInit': function() {
                
            },
            'onQueueComplete': function() {
                // this.uploadifive('clearQueue');
            }
        });
    }
};

var songUpload = {
    init: function (params) {
        params.buttonSelector = '#upload_song';
        params.uploadScript = '/bands/upload-song';
        params.buttonText = 'Select tracks to upload';
        params.fileType = ['audio/mpeg', 'audio/mp3'];
        mediaUpload.init(params);
    }
};
