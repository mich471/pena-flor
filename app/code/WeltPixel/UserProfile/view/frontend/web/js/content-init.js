define(
    [
      'jquery',
      'WeltPixel_UserProfile/js/content-tools'
    ],function   ($) {

      var WP_UserProfile = WP_UserProfile || {};

      var contentInit = {
        initialize: function(params) {

          WP_UserProfile.userprofileId = params.userprofileId;
          WP_UserProfile.customerId = params.customerId;
          WP_UserProfile.saveUrl = params.saveUrl;
          WP_UserProfile.imgUploadUrl = params.imgUploadUrl;

          var ImageUploader;
          ImageUploader = (function() {
            ImageUploader.imagePath = '';

            ImageUploader.imageSize = [600, 174];

            function ImageUploader(dialog) {
              this._dialog = dialog;
              this._dialog.addEventListener('cancel', (function(_this) {
                return function() {
                  return _this._onCancel();
                };
              })(this));
              this._dialog.addEventListener('imageuploader.cancelupload', (function(_this) {
                return function() {
                  return _this._onCancelUpload();
                };
              })(this));
              this._dialog.addEventListener('imageuploader.clear', (function(_this) {
                return function() {
                  return _this._onClear();
                };
              })(this));
              this._dialog.addEventListener('imageuploader.fileready', (function(_this) {
                return function(ev) {
                  return _this._onFileReady(ev.detail().file, ev);
                };
              })(this));
              this._dialog.addEventListener('imageuploader.mount', (function(_this) {
                return function() {
                  return _this._onMount();
                };
              })(this));
              this._dialog.addEventListener('imageuploader.rotateccw', (function(_this) {
                return function() {
                  return _this._onRotateCCW();
                };
              })(this));
              this._dialog.addEventListener('imageuploader.rotatecw', (function(_this) {
                return function() {
                  return _this._onRotateCW();
                };
              })(this));
              this._dialog.addEventListener('imageuploader.save', (function(_this) {
                return function() {
                  return _this._onSave();
                };
              })(this));
              this._dialog.addEventListener('imageuploader.unmount', (function(_this) {
                return function() {
                  return _this._onUnmount();
                };
              })(this));
            }

            ImageUploader.prototype._onCancel = function() {};

            ImageUploader.prototype._onCancelUpload = function() {
              clearTimeout(this._uploadingTimeout);
              return this._dialog.state('empty');
            };

            ImageUploader.prototype._onClear = function() {
              return this._dialog.clear();
            };

            ImageUploader.prototype._onFileReady = function(file, ev) {
              var upload, ajaxUploadUrl;

              ajaxUploadUrl = WP_UserProfile.imgUploadUrl;

              this._dialog.progress(0);
              this._dialog.state('uploading');

              upload = (function(_this) {
                return function() {
                  var formData;
                  var file = ev.detail().file;

                  // Build the form data to post to the server
                  formData = new FormData();
                  formData.append('image', file);
                  formData.append('customerId', WP_UserProfile.customerId);

                  if (jQuery("[data-name='avatar']").hasClass('ce-element--focused')) {
                    formData.append('image-field', 'avatar');
                  }

                  // Make the request
                  $.ajax({
                    xhr: function() {
                      //Instantiate XHR
                      var xhr = new window.XMLHttpRequest();
                      //Add Progress Event Listener
                      xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                          var percentComplete = evt.loaded / evt.total;
                          percentComplete = parseInt(percentComplete * 100);
                          _this._dialog.progress(percentComplete);
                        }
                      }, false);
                      return xhr;
                    },
                    method: 'POST',
                    url: ajaxUploadUrl,
                    data: formData,
                    processData:false,
                    cache: false,
                    contentType: false,
                    success: function(response) {
                      // Store the image details
                      image = {
                        size: response.size,
                        url: response.url,
                        w: response.width,
                        h: response.height
                      };

                      // Populate the dialog
                      _this._dialog.populate(image.url, image.size);

                      // Set img path and size
                      ImageUploader.imagePath = image.url;
                      ImageUploader.imageSize = [image.w,image.h];
                    }
                  });
                };
              })(this);

              return this._uploadingTimeout = setTimeout(upload, 25);
            };

            ImageUploader.prototype._onMount = function() {};

            ImageUploader.prototype._onRotateCCW = function() {
              var clearBusy;
              this._dialog.busy(true);
              clearBusy = (function(_this) {
                return function() {
                  return _this._dialog.busy(false);
                };
              })(this);
              return setTimeout(clearBusy, 1500);
            };

            ImageUploader.prototype._onRotateCW = function() {
              var clearBusy;
              this._dialog.busy(true);
              clearBusy = (function(_this) {
                return function() {
                  return _this._dialog.busy(false);
                };
              })(this);
              return setTimeout(clearBusy, 1500);
            };

            ImageUploader.prototype._onSave = function() {
              var clearBusy;
              this._dialog.busy(true);
              clearBusy = (function(_this) {
                return function() {
                  _this._dialog.busy(false);
                  return _this._dialog.save(ImageUploader.imagePath, ImageUploader.imageSize, {
                    alt: ''
                  });
                };
              })(this);
              return setTimeout(clearBusy, 1500);
            };

            ImageUploader.prototype._onUnmount = function() {};

            ImageUploader.createImageUploader = function(dialog) {
              return new ImageUploader(dialog);
            };

            return ImageUploader;

          })();

          WP_UserProfile.ImageUploader = ImageUploader;

          var FIXTURE_TOOLS, IMAGE_FIXTURE_TOOLS, LINK_FIXTURE_TOOLS, editor;

          ContentTools.IMAGE_UPLOADER = WP_UserProfile.ImageUploader.createImageUploader;
          editor = ContentTools.EditorApp.get();
          editor.init('[data-editable], [data-fixture]', 'data-name');
          FIXTURE_TOOLS = [['undo', 'redo', 'remove']];
          IMAGE_FIXTURE_TOOLS = [['undo', 'redo','remove', 'image']];
          LINK_FIXTURE_TOOLS = [['undo', 'redo', 'link']];
          ContentEdit.Root.get().bind('focus', function(element) {
            var tools;
            if (element.isFixed()) {
              if (element.type() === 'ImageFixture') {
                tools = IMAGE_FIXTURE_TOOLS;
              } else if (element.tagName() === 'a') {
                tools = LINK_FIXTURE_TOOLS;
              } else {
                tools = FIXTURE_TOOLS;
              }
            } else {
              tools = ContentTools.DEFAULT_TOOLS;
            }
            if (editor.toolbox().tools() !== tools) {
              return editor.toolbox().tools(tools);
            }
          });

          editor.addEventListener('revert', function(ev) {
              contentInit.stopEditingElementsDisplay({});
              $('.ct-editable').removeClass('ce-element--focused');
          });

          editor.addEventListener('saved', function (ev) {
            var name, payload, regions, ajaxUrl;

            // Check that something changed
            regions = ev.detail().regions;
            contentInit.updateRegionElements(regions);

            $('[data-editable],[data-fixture]').each(function(index, item) {
                var fieldName = $(item).attr('data-name');
                var fieldValue = $(item).html();
                if (!regions.hasOwnProperty(fieldName)) {
                  regions[fieldName] = fieldValue;
                }
            });

            // Set the editor as busy while we save our changes
            this.busy(true);

            // Collect the contents of each region into a FormData instance
            payload = new FormData();
            for (name in regions) {
              if (regions.hasOwnProperty(name)) {
                payload.append(name, regions[name]);
              }
            }



            ajaxUrl = WP_UserProfile.saveUrl;
            var req = $.ajax({
              method: 'POST',
              cache: false,
              global: false,
              url: ajaxUrl,
              data: regions
            });

            req.done(function(data) {
              var options = contentInit.getEditingElementOptions(data);
              contentInit.stopEditingElementsDisplay(options);

              if (data.result) {
                new ContentTools.FlashUI('ok');
              } else {
                new ContentTools.FlashUI('no');
              }

              if (data.redirect) {
                window.location.replace(data.redirect);
              }

              if (data.error) {
                $('.validation-errors').html(data.error).fadeIn();
                  $('html, body').animate({
                      scrollTop: ($('.validation-errors').offset().top)
                  }, 'slow');
              } else {
                  $('.validation-errors').html('').hide();
              }

              var elements = $('.ct-editable');
              elements.removeClass('ce-element--focused');
            });

            req.fail(function() {
              // Save failed, notify the user with a flash
              new ContentTools.FlashUI('no');
            });

            req.always(function() {
              // Make sure the editor is no longer set in a busy state
              ContentTools.EditorApp.get().busy(false);
            });

          });

          ContentEdit.Root.get().bind('ready', function (element) {
            var elm = element.domElement();
            var currElm = $(elm);

              contentInit.startEditingElementsDisplay();

            if(!currElm.hasClass('image-fixture')) {
              currElm.addClass('ce-element--focused');
            }

          });

          ContentEdit.Root.get().bind('focus', function (element) {
            $('.ct-editable').addClass('ce-element--focused');
              contentInit.startEditingElementsDisplay();
          });
        },
        startEditingElementsDisplay: function () {
            $('.ct-not-visible').removeClass('ct-not-visible').addClass('ct-inline-edit-visible');
            $('.profile-name').hide();
            $('.profile-details-content').hide();
            $('.profile-edit-hide').hide();
        },
        stopEditingElementsDisplay: function (options) {
          $('.ct-inline-edit-visible').removeClass('ct-inline-edit-visible').addClass('ct-not-visible');
          if (options.profileName) {
              $('.profile-name').html(options.profileName);
          }
          if (options.profileDetails) {
            $('.profile-details-content').html(options.profileDetails);
          }
          $('.profile-name').show();
          $('.profile-details-content').show();
          $('.profile-edit-hide').show();
        },
        getEditingElementOptions: function(data) {
          var options = {};
          options.profileName = data.profileName;
          options.profileDetails = data.profileDetails;
          return options;
        },
        updateRegionElements: function(regions) {
          if ( $("input[name='gender']:checked").val()) {
            regions.gender = $("input[name='gender']:checked").val();
          } else {
            regions.gender = '';
          }
          regions.dob = $("#dob").val();
          regions.profileId = WP_UserProfile.userprofileId;
          regions.customerId = WP_UserProfile.customerId;
        }

      };

      return contentInit;

  });
