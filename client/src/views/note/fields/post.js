

Espo.define('views/note/fields/post', ['views/fields/text', 'lib!Textcomplete'], function (Dep, Textcomplete) {

    return Dep.extend({

        rowsDefault: 1,

        seeMoreText: false,

        events: _.extend({
            'input textarea': function (e) {
                this.controlTextareaHeight();
            },
            'paste textarea': function (e) {
                if (!e.originalEvent.clipboardData) return;
                var text = e.originalEvent.clipboardData.getData('text/plain');
                if (!text) return;
                text = text.trim();
                if (!text) return;
                this.handlePastedText(text, e.originalEvent);
            }
        }, Dep.prototype.events),

        setup: function () {
            Dep.prototype.setup.call(this);

            this.insertedImagesData = {};
        },

        controlTextareaHeight: function (lastHeight) {
            var scrollHeight = this.$element.prop('scrollHeight');
            var clientHeight = this.$element.prop('clientHeight');

            if (clientHeight === lastHeight) return;

            if (scrollHeight > clientHeight) {
                this.$element.attr('rows', this.$element.prop('rows') + 1);
                this.controlTextareaHeight(clientHeight);
            }

            if (this.$element.val().length === 0) {
                this.$element.attr('rows', 1);
            }
        },

        afterRender: function () {
            Dep.prototype.afterRender.call(this);
            var placeholderText = this.options.placeholderText || this.translate('writeMessage', 'messages', 'Note');
            this.$element.attr('placeholder', placeholderText);

            this.$textarea = this.$element;
            var $textarea = this.$textarea;

            $textarea.off('drop');
            $textarea.off('dragover');
            $textarea.off('dragleave');

            this.$textarea.on('drop', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var e = e.originalEvent;
                if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
                    this.trigger('add-files', e.dataTransfer.files);
                }
                this.$textarea.attr('placeholder', originalPlaceholderText);
            }.bind(this));

            var originalPlaceholderText = this.$textarea.attr('placeholder');

            this.$textarea.on('dragover', function (e) {
                e.preventDefault();
                this.$textarea.attr('placeholder', this.translate('dropToAttach', 'messages'));
            }.bind(this));
            this.$textarea.on('dragleave', function (e) {
                e.preventDefault();
                this.$textarea.attr('placeholder', originalPlaceholderText);
            }.bind(this));

            var assignmentPermission = this.getAcl().get('assignmentPermission');

            var buildUserListUrl = function (term) {
                var url = 'User?q=' + term + '&' + $.param({'primaryFilter': 'active'}) +
                    'orderBy=name&maxSize=' + this.getConfig().get('recordsPerPage') +
                    '&select=id,name,userName';
                if (assignmentPermission == 'team') {
                    url += '&' + $.param({'boolFilterList': ['onlyMyTeam']})
                }
                return url;
            }.bind(this);

            if (assignmentPermission !== 'no') {
                this.$element.textcomplete([{
                    match: /(^|\s)@(\w*)$/,
                    search: function (term, callback) {
                        if (term.length == 0) {
                            callback([]);
                            return;
                        }
                        Espo.Ajax.getRequest(buildUserListUrl(term)).then(function (data) {
                            callback(data.list)
                        });
                    },
                    template: function (mention) {
                        return mention.name + ' <span class="text-muted">@' + mention.userName + '</span>';
                    },
                    replace: function (o) {
                        return '$1@' + o.userName + '';
                    }
                }],{
                    zIndex: 1100
                });

                this.once('remove', function () {
                    if (this.$element.length) {
                        this.$element.textcomplete('destroy');
                    }
                }, this);
            }
        },

        validateRequired: function () {
            if (this.isRequired()) {
                if ((this.model.get('attachmentsIds') || []).length) {
                    return false;
                }
            }
            return Dep.prototype.validateRequired.call(this);
        },

        handlePastedText: function (text, event) {
            if (/^http(s){0,1}\:\/\//.test(text)) {
                var imageExtensionList = ['jpg', 'jpeg', 'png', 'gif'];
                var regExpString = '.+\\.(' + imageExtensionList.join('|') + ')(/?.*){0,1}$';
                var regExp = new RegExp(regExpString, 'i');
                var url = text;
                var siteUrl = this.getConfig().get('siteUrl').replace(/\/$/, '');

                var attachmentIdList = this.model.get('attachmentsIds') || [];

                if (regExp.test(text)) {
                    var insertedId = this.insertedImagesData[url];
                    if (insertedId) {
                        if (~attachmentIdList.indexOf(insertedId)) return;
                    }

                    this.ajaxPostRequest('Attachment/action/getAttachmentFromImageUrl', {
                        url: url,
                        parentType: 'Note',
                        field: 'attachments'
                    }).then(function (attachment) {
                        var attachmentIdList = Espo.Utils.clone(this.model.get('attachmentsIds') || []);
                        var attachmentNames = Espo.Utils.clone(this.model.get('attachmentsNames') || {});
                        var attachmentTypes = Espo.Utils.clone(this.model.get('attachmentsTypes') || {});

                        attachmentIdList.push(attachment.id);
                        attachmentNames[attachment.id] = attachment.name;
                        attachmentTypes[attachment.id] = attachment.type;

                        this.insertedImagesData[url] = attachment.id;

                        this.model.set({
                            attachmentsIds: attachmentIdList,
                            attachmentsNames: attachmentNames,
                            attachmentsTypes: attachmentTypes
                        });
                    }.bind(this)).fail(function (xhr) {
                        xhr.errorIsHandled = true;
                    });

                } else if (/\?entryPoint\=image\&/.test(text) && text.indexOf(siteUrl) === 0) {
                    url = text.replace(/[\&]{0,1}size\=[a-z\-]*/, '');

                    var match = /\&{0,1}id\=([a-z0-9A-Z]*)/g.exec(text)
                    if (match.length === 2) {
                        var id = match[1];
                        if (~attachmentIdList.indexOf(id)) return;
                        var insertedId = this.insertedImagesData[id];
                        if (insertedId) {
                            if (~attachmentIdList.indexOf(insertedId)) return;
                        }

                        this.ajaxPostRequest('Attachment/action/getCopiedAttachment', {
                            id: id,
                            parentType: 'Note',
                            field: 'attachments'
                        }).then(function (attachment) {
                            var attachmentIdList = Espo.Utils.clone(this.model.get('attachmentsIds') || []);
                            var attachmentNames = Espo.Utils.clone(this.model.get('attachmentsNames') || {});
                            var attachmentTypes = Espo.Utils.clone(this.model.get('attachmentsTypes') || {});

                            attachmentIdList.push(attachment.id);
                            attachmentNames[attachment.id] = attachment.name;
                            attachmentTypes[attachment.id] = attachment.type;

                            this.insertedImagesData[id] = attachment.id;

                            this.model.set({
                                attachmentsIds: attachmentIdList,
                                attachmentsNames: attachmentNames,
                                attachmentsTypes: attachmentTypes
                            });
                        }.bind(this)).fail(function (xhr) {
                            xhr.errorIsHandled = true;
                        });
                    }
                }
            }
        }

    });
});
