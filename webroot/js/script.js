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
                var response = $.parseJSON(data);
                if (response.success) {
                    params.uploadComplete(response);
                } else {
                    alert(response.message);
                }
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
        params.fileType = ['audio/mpeg', 'audio/mp3', 'audio/mpeg3', 'audio/x-mpeg-3'];
        params.uploadComplete = this.uploadComplete;
        mediaUpload.init(params);
    },
    
    uploadComplete: function (response) {
        
    }
};

var pictureUpload = {
    init: function (params) {
        if ($('#uploadedImages li').length === 0) {
            $('#uploadedImages').hide();
        }
        
        params.buttonSelector = '#upload_picture';
        params.uploadScript = '/bands/upload-picture';
        params.buttonText = 'Select picture to upload';
        params.fileType = ['image/png', 'image/jpeg', 'image/gif'];
        params.uploadComplete = this.uploadComplete;
        mediaUpload.init(params);
    },
    
    uploadComplete: function (response) {
        var container = $('#uploadedImages');
        var imageCount = container.find('li').length;
        var li = $('<li></li>');
        
        var image = '<img src="/img/bands/thumb/'+response.filename+'" alt="'+response.filename+'" title="Click for full-size" />';
        var link = '<a href="/img/bands/'+response.filename+'" target="_blank">'+image+'</a>';
        li.append(link);
        
        var primaryButton = '<input type="radio" name="primaryPictureId" value="'+response.pictureId+'" id="picturePrimary'+response.pictureId+'"';
        if (imageCount === 0) {
            primaryButton += ' checked="checked"';
        }
        primaryButton += ' />';
        var label = '<label for="picturePrimary'+response.pictureId+'">'+primaryButton+' Main image</label>';
        li.append(label);
        
        container.find('ul').append(li);
        
        if (! container.is('visible')) {
            container.slideDown();
        }
    }
};
