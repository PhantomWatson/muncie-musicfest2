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
        if ($('#uploadedSongs tbody tr').length === 0) {
            $('#uploadedSongs').hide();
        }
        
        params.buttonSelector = '#upload_song';
        params.uploadScript = '/bands/upload-song';
        params.buttonText = 'Select tracks to upload';
        params.fileType = ['audio/mpeg', 'audio/mp3', 'audio/mpeg3', 'audio/x-mpeg-3'];
        params.uploadComplete = this.uploadComplete;
        mediaUpload.init(params);
        
        $('#applicationForm').submit(function (event) {
            
            // Prevent identical track names
            var inputs = $('#uploadedSongs input[type=text]');
            if (inputs.length < 2) {
                return;
            }
            var trackTitles = [];
            inputs.each(function () {
                trackTitles.push($(this).val());
            });
            for (var i = 0; i < trackTitles.length; i++) {
                var trackTitle = trackTitles.pop();
                if (trackTitles.indexOf(trackTitle) !== -1) {
                    alert('Hold up! Two of the tracks you uploaded have the same title: '+trackTitle);
                    event.preventDefault();
                }
            }
        });
    },
    
    uploadComplete: function (response) {
        var container = $('#uploadedSongs');
        var songCount = container.find('tbody tr').length;
        var row = $('<tr></tr>');
        var i = songCount;
        
        var inputs = '<input class="form-control" type="text" name="songs['+i+'][title]" placeholder="Track title" required="required" maxlength="100" id="songs-'+i+'-title" value="'+response.trackName+'" />';
        inputs += '<input class="form-control" type="hidden" name="songs['+i+'][id]" id="songs-'+i+'-id" value="'+response.songId+'" />';
        var cell = '<td>'+inputs+'</td>';
        row.append(cell);
        
        var icon = '<span class="glyphicon glyphicon-music" aria-hidden="true"></span><span class="sr-only">Play</span>';
        var link = '<a href="/music/'+response.filename+'" target="_blank">'+icon+'</a>';
        cell = '<td>'+link+'</td>';
        row.append(cell);
    
        var deleteCheckbox = '<input type="checkbox" name="deleteSongs[]" value="'+response.songId+'" />';
        cell = '<td>'+deleteCheckbox+'</td>';
        row.append(cell);
        
        container.find('tbody').append(row);
        
        if (! container.is('visible')) {
            container.slideDown();
        }
        
        songUpload.checkUploadLimit();
    },
    
    checkUploadLimit: function () {
        var limit = 3;
        var songCount = $('#uploadedSongs tbody tr').length;
        if (songCount >= limit) {
            $('#songLimitReached').slideDown();
            $('#uploadSongContainer').slideUp(300, function () {
                $(this).remove();
            });
        }
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
        
        var deleteCheckbox = '<input id="pictureDelete'+response.pictureId+'" type="checkbox" name="deletePictures[]" value="'+response.pictureId+'" />';
        label = '<label for="pictureDelete'+response.pictureId+'">'+deleteCheckbox+' Delete</label>';
        li.append(label);
        
        container.find('ul').append(li);
        
        if (! container.is('visible')) {
            container.slideDown();
        }
        
        pictureUpload.checkUploadLimit();
    },
    
    checkUploadLimit: function () {
        var limit = 3;
        var imageCount = $('#uploadedImages li').length;
        if (imageCount >= limit) {
            $('#pictureLimitReached').slideDown();
            $('#uploadPictureContainer').slideUp(300, function () {
                $(this).remove();
            });
        }
    }
};
