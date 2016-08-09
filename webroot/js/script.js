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
            multi: false,
            removeCompleted: true,
            queueSizeLimit: params.queueSizeLimit,
            uploadLimit: params.queueSizeLimit,
            uploadScript: params.uploadScript,
            width: 250,
            
            onCheck: false,
            onUpload: function (filesToUpload) {
                
            },
            'onUploadComplete': function (file, data) {
                var response = $.parseJSON(data);
                if (response.success) {
                    params.uploadComplete(response);
                } else {
                    alert(response.message);
                }
            },
            'onFallback': function () {
                // Warn user that their browser is old
            },
            'onError': function (errorType, files) {
                var response = $.parseJSON(files.xhr.responseText);
                alert('There was an error uploading '+files.name+' ('+response.message+')');
            },
            'onInit': function () {
                
            },
            'onQueueComplete': function () {
                // this.uploadifive('clearQueue');
            },
            'onAddQueueItem': function (file) {
                var ext = file.name.substr(file.name.lastIndexOf('.') + 1);
                ext = ext.toLowerCase();
                var validFileTypes = params.fileType.split(',');
                var valid = false;
                var validExt = '';
                for (var i = 0; i < validFileTypes.length; i++) {
                    validExt = validFileTypes[i].replace('.', '');
                    validExt = validExt.toLowerCase();
                    if (ext == validExt) {
                        valid = true;
                    }
                }
                if (! valid) {
                    $(params.buttonSelector).uploadifive('cancel', file);
                    alert('Sorry, '+file.name+' isn\'t one of these file types: '+validFileTypes.join(', ')); 
                }
            }
        });
    }
};

var applicationForm = {
    initPictures: function (params) {
        this.linkPictures();
        this.pictureUpload.init(params.uploadParams);
        $('.delete-picture').click(function (event) {
            event.preventDefault();
            applicationForm.deletePicture($(this));
        });
        $('.delete-song').click(function (event) {
            event.preventDefault();
            applicationForm.deleteSong($(this));
        });
    },
    
    initSongs: function (params) {
        this.songUpload.init(params.uploadParams);
    },
        
    /* Wraps thumbnails in links to full-size version of picture.
     * Doing this in JS allow CakePHP's HtmlHelper to add timestamps
     * to the pictures' src and have those timestamps copied over
     * into the links. */
    linkPictures: function () {
        $('#uploadedImages img').each(function () {
            var img = $(this);
            var url = img.prop('src').replace('/img/bands/thumb', '/img/bands');
            var link = $('<a href="'+url+'" target="_blank"></a>');
            img.wrap(link);
            img.attr('title', 'Click for full-size');
            img.closest('a').magnificPopup({type:'image'});
        });
    },
    
    deletePicture: function (button) {
        if (! confirm('Are you sure you want to delete this picture? Pinky swear?')) {
            return;
        }
        var pictureId = button.data('picture-id');
        $.ajax({
            url: '/bands/delete-picture/',
            data: {pictureId: pictureId},
            method: 'POST',
            beforeSend: function () {
                button.html('Deleting...');
                button.prop('disabled', true);
            },
            success: function () {
                button.removeClass('btn-danger').addClass('btn-success').html('Deleted');
                button.closest('li').addClass('deleted');
                applicationForm.pictureUpload.checkUploadLimit();
                setTimeout(function () {
                    var container = button.closest('li');
                    container.fadeOut(500, function () {
                        $(this).remove();
                    });
                }, 3000);
            },
            error: function () {
                button.html('Delete');
                button.prop('disabled', false);
            },
            statusCode: {
                403: function () {
                    alert('Sorry, you\'re not authorized to delete that picture');
                },
                404: function () {
                    alert('Sorry, that picture was not found. (Maybe because it has already been deleted)');
                },
                500: function () {
                    alert('There was an error deleting that image.');
                }
            }
        });
    },
    
    pictureUpload: {
        limit: null,
            
        init: function (params) {
            if ($('#uploadedImages li').length === 0) {
                $('#uploadedImages').hide();
            }
            
            this.limit = params.limit;
            params.buttonSelector = '#upload_picture';
            params.buttonText = 'Select picture to upload';
            params.fileType = '.png,.jpeg,.jpg,.gif';
            params.queueSizeLimit = 3;
            params.uploadComplete = this.uploadComplete;
            params.uploadScript = '/bands/upload-picture';
            mediaUpload.init(params);
        },
        
        uploadComplete: function (response) {
            var container = $('#uploadedImages');
            var imageCount = container.find('li').length;
            var li = $('<li></li>');
            
            var timestamp = Math.floor(Date.now() / 1000);
            var image = '<img src="/img/bands/thumb/'+response.filename+'?'+timestamp+'" alt="'+response.filename+'" title="Click for full-size" />';
            var link = $('<a href="/img/bands/'+response.filename+'?'+timestamp+'" target="_blank">'+image+'</a>');
            link.magnificPopup({type:'image'});
            li.append(link);
            
            var primaryButton = '<input type="radio" name="primaryPictureId" value="'+response.pictureId+'" id="picturePrimary'+response.pictureId+'"';
            if (imageCount === 0) {
                primaryButton += ' checked="checked"';
            }
            primaryButton += ' />';
            var label = '<label for="picturePrimary'+response.pictureId+'">'+primaryButton+' Main image</label>';
            li.append(label);
            
            var deleteButton = $('<button>Delete</button>');
            deleteButton.addClass('btn btn-danger btn-xs delete-picture').data('picture-id', response.pictureId);
            deleteButton.click(function (event) {
                event.preventDefault();
                applicationForm.deletePicture(deleteButton);
            });
            li.append(deleteButton);
            
            container.find('ul').append(li);
            
            if (! container.is('visible')) {
                container.slideDown();
            }
            
            applicationForm.pictureUpload.checkUploadLimit();
        },
        
        checkUploadLimit: function () {
            var imageCount = $('#uploadedImages li').not('.deleted').length;
            var limitMessage = $('#pictureLimitReached');
            var uploadContainer = $('#uploadPictureContainer');
            if (imageCount >= this.limit) {
                if (uploadContainer.is(':visible')) {
                    limitMessage.slideDown();
                    uploadContainer.slideUp();
                }
            } else if (! uploadContainer.is(':visible')) {
                limitMessage.slideUp();
                uploadContainer.slideDown();
            }
        }
    },
    
    songUpload: {
        limit: null,
        
        init: function (params) {
            if ($('#uploadedSongs tbody tr').length === 0) {
                $('#uploadedSongs').hide();
            }
            this.limit = params.limit;
            params.buttonSelector = '#upload_song';
            params.buttonText = 'Select track to upload';
            params.fileType = '.mp3';
            params.queueSizeLimit = this.limit;
            params.uploadComplete = this.uploadComplete;
            params.uploadScript = '/bands/upload-song';
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
            var inputContainer = '<div class="form-group text required">'+inputs+'</a>';
            var cell = '<td>'+inputContainer+'</td>';
            row.append(cell);
            
            var icon = '<span class="glyphicon glyphicon-music" aria-hidden="true"></span><span class="sr-only">Play</span>';
            var link = '<a href="/music/'+response.filename+'" target="_blank">'+icon+'</a>';
            cell = '<td>'+link+'</td>';
            row.append(cell);
        
            var deleteButton = $('<button>Delete</button>');
            deleteButton.addClass('btn btn-danger btn-xs delete-song').data('song-id', response.songId);
            deleteButton.click(function (event) {
                event.preventDefault();
                applicationForm.deleteSong(deleteButton);
            });
            cell = $('<td></td>');
            cell.append(deleteButton);
            row.append(cell);
            
            container.find('tbody').append(row);
            
            if (! container.is('visible')) {
                container.slideDown();
            }
            
            applicationForm.songUpload.checkUploadLimit();
        },
        
        checkUploadLimit: function () {
            var songCount = $('#uploadedSongs tbody tr').not('.deleted').length;
            var limitMessage = $('#songLimitReached');
            var uploadContainer = $('#uploadSongContainer');
            if (songCount >= this.limit) {
                if (uploadContainer.is(':visible')) {
                    limitMessage.slideDown();
                    uploadContainer.slideUp();
                }
            } else if (! uploadContainer.is(':visible')) {
                limitMessage.slideUp();
                uploadContainer.slideDown();
            }
        }
    },
    
    deleteSong: function (button) {
        if (! confirm('Are you sure you want to delete this song? For reals?')) {
            return;
        }
        var songId = button.data('song-id');
        $.ajax({
            url: '/bands/delete-song/',
            data: {songId: songId},
            method: 'POST',
            beforeSend: function () {
                button.html('Deleting...');
                button.prop('disabled', true);
            },
            success: function () {
                button.removeClass('btn-danger').addClass('btn-success').html('Deleted');
                button.closest('tr').addClass('deleted');
                applicationForm.songUpload.checkUploadLimit();
                setTimeout(function () {
                    var container = button.closest('tr');
                    container.fadeOut(500, function () {
                        $(this).remove();
                    });
                }, 3000);
            },
            error: function () {
                button.html('Delete');
                button.prop('disabled', false);
            },
            statusCode: {
                403: function () {
                    alert('Sorry, you\'re not authorized to delete that song');
                },
                404: function () {
                    alert('Sorry, that song was not found. (Maybe because it has already been deleted)');
                },
                500: function () {
                    alert('There was an error deleting that song.');
                }
            }
        });
    }
};

var bandSelector = {
    init: function (prefix) {
        $('#band-selector').change(function () {
            var option = $(this).find('option:selected');
            window.location.href = option.data('url');
        });
    }
};

var musicPlayer = {
    playlist: null,

    init: function () {
        var cssSelector = {
            cssSelectorAncestor: "#jp_container",
            jPlayer: "#jquery_jplayer_1"
        };
        var songs = [];
        var options = {
            playlistOptions: {
                autoPlay: false,
                loopOnPrevious: true,
                shuffleOnLoop: true,
                enableRemoveControls: false,
                displayTime: 0,
                addTime: 0,
                removeTime: 0,
                shuffleTime: 0,
            },
            supplied: "mp3",
            swfPath: "/js",
            toggleDuration: true
        };
        this.playlist = new jPlayerPlaylist(cssSelector, songs, options);

        /**
         * This should be happening automatically, but for some reason isn't.
         */
        $('#jp_container button.jp-play').click(function (event) {
            if ($('#jp_container').hasClass('jp-state-playing')) {
                musicPlayer.playlist.pause();
            }
        });
    }
};

var imagePopups = {
    /* Wraps thumbnails in links to full-size version of picture.
     * Doing this in JS allow CakePHP's HtmlHelper to add timestamps
     * to the pictures' src and have those timestamps copied over
     * into the links. */
    linkPictures: function () {
        $('img.thumbnail-popup').each(function () {
            var img = $(this);
            var url = img.prop('src').replace('/img/bands/thumb', '/img/bands');
            var link = $('<a href="'+url+'" target="_blank"></a>');
            img.wrap(link);
            img.attr('title', 'Click for full-size');
            img.closest('a').magnificPopup({type:'image'});
        });
    }
};

var scheduleEditor = {
    bands: [],

    init: function (bands) {
        this.bands = bands;
        $('#edit-slots tbody tr').each(function () {
            var row = $(this);
            if (row.data('band-id') == '') {
                scheduleEditor.markAsUnbooked(row);
            } else {
                var bandId = row.data('band-id');
                var bandName = $('#bands-master-list option[value=' + bandId + ']').text();
                scheduleEditor.markAsBooked(row, bandId, bandName);
            }
        });
        this.hideAllBookedBands();
        this.hideIncompleteApplications();
        $('#show-incomplete-applications').change(function () {
            if ($(this).is(':checked')) {
                scheduleEditor.showIncompleteApplications();
            } else {
                scheduleEditor.hideIncompleteApplications();
            }
        });
        $('#bands-master-list').change(function () {
            var bandId = $(this).val();
            if (bandId == '') {
                return;
            }
            $.ajax({
                url: '/admin/bands/view/' + bandId,
                beforeSend: function () {
                    var loading = $('<img src="/img/loading_small.gif" alt="Loading..." id="ajax-loading" />');
                    $('#bands-master-list').after(loading);
                },
                error: function () {

                },
                success: function (data, textStatus, jqXHR) {
                    var container = $('#band-profile-ajax');
                    if (container.is(':visible')) {
                        container.scrollTop(0);
                        container.slideUp(300, function () {
                            container.html(data);
                            container.slideDown();
                        })
                    } else {
                        container.html(data);
                        container.slideDown();
                    }
                },
                complete: function () {
                    $('#ajax-loading').remove();
                }
            })
        });
    },

    markAsUnbooked: function (row, bandId) {
        var bandCell = row.find('td.band');
        bandCell.html('<span class="text-muted">Not booked</span> ');
        var slotKey = row.data('slot-key');
        var hiddenInput = $('<input type="hidden" name="slots[' + slotKey + '][band_id]" value="" />');
        bandCell.append(hiddenInput);

        var actionCell = row.find('td.band-action');
        var button = $('<button class="btn btn-default" title="Select a band..."></button>');
        button.append('<span class="glyphicon glyphicon-list" aria-hidden="true"></span>');
        button.append('<span class="sr-only">Edit</span>');
        button.click(function (event) {
            event.preventDefault();
            scheduleEditor.openBandSelector(row);
        });
        actionCell.html(button);

        if (bandId) {
            this.unhideBand(bandId);
        }
    },

    openBandSelector: function (row) {
        var bandCell = row.find('td.band');
        var bandSelector = $('#bands-master-list').clone();
        bandSelector.prepend('<option>Select a band...</option>');
        bandSelector[0].selectedIndex = 0;
        bandSelector.change(function () {
            var selector = $(this);
            if (selector.val() == '') {
                selector[0].selectedIndex = 0;
            } else {
                var bandId = selector.val();
                var bandName = selector.find('option:selected').text();
                scheduleEditor.markAsBooked(row, bandId, bandName);
            }
        });
        bandCell.html(bandSelector);
        var slotKey = row.data('slot-key');
        var hiddenInput = $('<input type="hidden" name="slots[' + slotKey + '][band_id]" value="" />');
        bandCell.append(hiddenInput);

        var actionCell = row.find('td.band-action');
        var button = $('<button class="btn btn-default" title="Cancel"></button>');
        button.append('<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>');
        button.append('<span class="sr-only">Cancel</span>');
        button.click(function (event) {
            event.preventDefault();
            scheduleEditor.markAsUnbooked(row);
        });
        actionCell.html(button);
    },

    markAsBooked: function (row, bandId, bandName) {
        var bandCell = row.find('td.band');
        bandCell.html(bandName);
        var slotKey = row.data('slot-key');
        var hiddenInput = $('<input type="hidden" name="slots[' + slotKey + '][band_id]" value="' + bandId + '" />');
        bandCell.append(hiddenInput);

        var actionCell = row.find('td.band-action');
        var button = $('<button class="btn btn-default" title="Remove"></button>');
        button.append('<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>');
        button.append('<span class="sr-only">Remove</span>');
        button.click(function (event) {
            event.preventDefault();
            scheduleEditor.markAsUnbooked(row, bandId);
        });
        actionCell.html(button);

        this.hideBand(bandId);
    },

    hideAllBookedBands: function () {
        $.each(this.bands, function (bandId, band) {
            if (band.booked) {
                scheduleEditor.hideBand(bandId);
            }
        });
    },

    hideBand: function (bandId) {
        $('select.band-selector option[value=' + bandId + ']').hide();
    },

    unhideBand: function (bandId) {
        $('select.band-selector option[value=' + bandId + ']').show();
    },

    hideIncompleteApplications: function () {
        $('select.band-selector').addClass('hide-incomplete-applications');
    },

    showIncompleteApplications: function () {
        $('select.band-selector').removeClass('hide-incomplete-applications');
    }
};

var bandConfirmations = {
    init: function () {
        $('td.confirmation-state').each(function () {
            var cell = $(this);
            var state = cell.data('confirmation-state');
            cell.prepend('<span class="label"></span>');
            bandConfirmations.updateConfirmationLabel(cell, state);
        });

        $('button.edit-confirmation').click(function (event) {
            event.preventDefault();
            var button = $(this);
            button.hide();
            var cell = button.parent('td');
            var label = cell.find('.label');
            label.hide();

            var select = $('<select></select>');
            select.append('<option>Select...</option>');
            select.append('<option value="not contacted">Not contacted</option>');
            select.append('<option value="contacted">Contacted</option>');
            select.append('<option value="confirmed">Confirmed</option>');
            select.append('<option value="dropped out">Dropped out</option>');
            select.insertAfter(button);

            select.change(function () {
                var confirmationState = select.find('option:selected').val();
                $.ajax({
                    url: button.data('url') + '/' + confirmationState,
                    beforeSend: function () {
                        var loading = $('<img src="/img/loading_small.gif" alt="Loading..." class="ajax-loading" />');
                        select.after(loading);
                    },
                    error: function () {
                        alert('There was an error updating that band\'s confirmation state.');
                    },
                    success: function (data, textStatus, jqXHR) {
                        bandConfirmations.updateConfirmationLabel(cell, confirmationState);
                    },
                    complete: function () {
                        select.next('img.ajax-loading').remove();
                        select.remove();
                        button.show();
                        label.show();
                    }
                });
            });
        });

        $('.edit-notes').click(function (event) {
            event.preventDefault();
            var editButton = $(this);
            var cell = editButton.parent('td');
            var notes = cell.find('.notes');

            notes.hide();
            editButton.hide();
            var input = $('<textarea></textarea>');
            var fixedNotes = notes.html().replace(/<br>/g, '').trim();
            input.html(fixedNotes);
            cell.append(input);

            var submitButton = $('<button class="btn btn-primary btn-sm update">Update</button>');
            var cancelButton = $('<button class="btn btn-default btn-sm cancel">Cancel</button>');
            cell.append(submitButton);
            cell.append(cancelButton);

            submitButton.click(function (event) {
                event.preventDefault();
                $.ajax({
                    url: editButton.data('url'),
                    method: 'POST',
                    data: {
                        admin_notes: input.val().trim()
                    },
                    beforeSend: function () {
                        var loading = $('<img src="/img/loading_small.gif" alt="Loading..." class="ajax-loading" />');
                        submitButton.prop('disabled', true);
                        cancelButton.prop('disabled', true);
                        submitButton.append(loading);
                    },
                    error: function () {
                        alert('There was an error updating that band\'s notes.');
                        submitButton.prop('disabled', false);
                        submitButton.find('img').remove();
                        cancelButton.prop('disabled', false);
                    },
                    success: function (data, textStatus, jqXHR) {
                        var displayedNotes = input.val().trim().replace(/\n/g, '<br>');
                        notes.html(displayedNotes);
                        input.remove();
                        submitButton.remove();
                        cancelButton.remove();
                        notes.show();
                        editButton.show();
                    }
                });
            });
            cancelButton.click(function (event) {
                event.preventDefault();
                input.remove();
                submitButton.remove();
                cancelButton.remove();
                notes.show();
                editButton.show();
            });
        });

        $('button.copy-message').click(function (event) {
            event.preventDefault();
            var button = $(this);
            var textarea = button.next('textarea.generated-message');
            var textareaId = textarea.attr('id');
            textarea.show();
            if (copyToClipboard(document.getElementById(textareaId))) {
                button.addClass('btn-success').removeClass('btn-default');
            } else {
                button.addClass('btn-danger').removeClass('btn-default');
            }
            textarea.hide();
        });
    },

    updateConfirmationLabel: function (cell, state) {
        var labelType = '';
        switch (state) {
            case 'not contacted':
                labelType = 'danger'
                break;
            case 'contacted':
                labelType = 'warning'
                break;
            case 'confirmed':
                labelType = 'success'
                break;
            case 'dropped out':
                labelType = 'danger'
                break;
            default:
                state = 'not contacted';
                labelType = 'danger';
        }
        var label = cell.find('.label');
        label.html(state);
        label.attr('class', 'label label-' + labelType);
    }
};

function copyToClipboard(elem) {
    // create hidden text element, if it doesn't already exist
    var targetId = "_hiddenCopyText_";
    var isInput = elem.tagName === "INPUT" || elem.tagName === "TEXTAREA";
    var origSelectionStart, origSelectionEnd;
    if (isInput) {
        // can just use the original source element for the selection and copy
        target = elem;
        origSelectionStart = elem.selectionStart;
        origSelectionEnd = elem.selectionEnd;
    } else {
        // must use a temporary form element for the selection and copy
        target = document.getElementById(targetId);
        if (!target) {
            var target = document.createElement("textarea");
            target.style.position = "absolute";
            target.style.left = "-9999px";
            target.style.top = "0";
            target.id = targetId;
            document.body.appendChild(target);
        }
        target.textContent = elem.textContent;
    }
    // select the content
    var currentFocus = document.activeElement;
    target.focus();
    target.setSelectionRange(0, target.value.length);

    // copy the selection
    var succeed;
    try {
        succeed = document.execCommand("copy");
    } catch(e) {
        succeed = false;
    }
    // restore original focus
    if (currentFocus && typeof currentFocus.focus === "function") {
        currentFocus.focus();
    }

    if (isInput) {
        // restore prior selection
        elem.setSelectionRange(origSelectionStart, origSelectionEnd);
    } else {
        // clear temporary content
        target.textContent = "";
    }
    return succeed;
};
