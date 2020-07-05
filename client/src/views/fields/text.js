

Espo.define('views/fields/text', 'views/fields/base', function (Dep) {

    return Dep.extend({

        type: 'text',

        listTemplate: 'fields/text/list',

        detailTemplate: 'fields/text/detail',

        editTemplate: 'fields/text/edit',

        searchTemplate: 'fields/text/search',

        seeMoreText: false,

        rowsDefault: 10,

        rowsMin: 2,

        seeMoreDisabled: false,

        cutHeight: 200,

        searchTypeList: ['contains', 'startsWith', 'equals', 'endsWith', 'like', 'notContains', 'notLike', 'isEmpty', 'isNotEmpty'],

        events: {
            'click a[data-action="seeMoreText"]': function (e) {
                this.seeMoreText = true;
                this.reRender();
            },
            'click [data-action="mailTo"]': function (e) {
                this.mailTo($(e.currentTarget).data('email-address'));
            },
        },

        setup: function () {
            Dep.prototype.setup.call(this);
            this.params.rows = this.params.rows || this.rowsDefault;

            this.seeMoreDisabled = this.seeMoreDisabled || this.params.seeMoreDisabled;

            this.autoHeightDisabled = this.options.autoHeightDisabled || this.params.autoHeightDisabled || this.autoHeightDisabled;

            if (this.params.cutHeight) {
                this.cutHeight = this.params.cutHeight;
            }

            if (this.params.rows < this.rowsMin) {
                this.rowsMin = this.params.rows;
            }

            this.on('remove', function () {
                $(window).off('resize.see-more-' + this.cid);
            }, this);
        },

        setupSearch: function () {
            this.events = _.extend({
                'change select.search-type': function (e) {
                    var type = $(e.currentTarget).val();
                    this.handleSearchType(type);
                },
            }, this.events || {});
        },

        data: function () {
            var data = Dep.prototype.data.call(this);
            if (
                this.model.get(this.name) !== null
                &&
                this.model.get(this.name) !== ''
                &&
                this.model.has(this.name)
            ) {
                data.isNotEmpty = true;
            }
            if (this.mode === 'search') {
                if (typeof this.searchParams.value === 'string') {
                    this.searchData.value = this.searchParams.value;
                }
            }
            if (this.mode === 'edit') {
                if (this.autoHeightDisabled) {
                    data.rows = this.params.rows;
                } else {
                    data.rows = this.rowsMin;
                }
            }
            data.valueIsSet = this.model.has(this.name);

            if (this.isReadMode()) {
                data.isCut = this.isCut();

                if (data.isCut) {
                    data.cutHeight = this.cutHeight;
                }

                data.displayRawText = this.params.displayRawText;
            }

            return data;
        },

        handleSearchType: function (type) {
            if (~['isEmpty', 'isNotEmpty'].indexOf(type)) {
                this.$el.find('input.main-element').addClass('hidden');
            } else {
                this.$el.find('input.main-element').removeClass('hidden');
            }
        },

        getValueForDisplay: function () {
            var text = this.model.get(this.name);
            return text || '';
        },

        controlTextareaHeight: function (lastHeight) {
            var scrollHeight = this.$element.prop('scrollHeight');
            var clientHeight = this.$element.prop('clientHeight');

            if (typeof lastHeight === 'undefined' && clientHeight === 0) {
                setTimeout(this.controlTextareaHeight.bind(this), 10);
                return;
            }

            if (clientHeight === lastHeight) return;

            if (scrollHeight > clientHeight + 1) {
                var rows = this.$element.prop('rows');

                if (this.params.rows && rows >= this.params.rows) return;

                this.$element.attr('rows', rows + 1);
                this.controlTextareaHeight(clientHeight);
            }
            if (this.$element.val().length === 0) {
                this.$element.attr('rows', this.rowsMin);
            }
        },

        isCut: function () {
            return !this.seeMoreText && !this.seeMoreDisabled;
        },

        controlSeeMore: function () {
            if (!this.isCut()) return;

            if (this.$text.height() > this.cutHeight) {
                this.$seeMoreContainer.removeClass('hidden');
                this.$textContainer.addClass('cut');
            } else {
                this.$seeMoreContainer.addClass('hidden');
                this.$textContainer.removeClass('cut');
            }
        },

        afterRender: function () {
            Dep.prototype.afterRender.call(this);

            if (this.isReadMode()) {
                $(window).off('resize.see-more-' + this.cid);

                this.$textContainer = this.$el.find('> .complex-text-container');
                this.$text = this.$textContainer.find('> .complex-text');
                this.$seeMoreContainer = this.$el.find('> .see-more-container');

                if (this.isCut()) {
                    this.controlSeeMore();
                    if (this.model.get(this.name) && this.$text.height() === 0) {
                        this.$textContainer.addClass('cut');
                        setTimeout(this.controlSeeMore.bind(this), 50);
                    }

                    $(window).on('resize.see-more-' + this.cid, function () {
                        this.controlSeeMore();
                    }.bind(this));
                }
            }

            if (this.mode == 'edit') {
                var text = this.getValueForDisplay();
                if (text) {
                    this.$element.val(text);
                }
            }
            if (this.mode == 'search') {
                var type = this.$el.find('select.search-type').val();
                this.handleSearchType(type);
            }

            if (this.mode === 'edit' && !this.autoHeightDisabled) {
                this.controlTextareaHeight();
                this.$element.on('input', function () {
                    this.controlTextareaHeight();
                }.bind(this));
            }
        },

        fetch: function () {
            var data = {};
            data[this.name] = this.$element.val() || null;
            return data;
        },

        fetchSearch: function () {
            var type = this.fetchSearchType() || 'startsWith';

            var data;

            if (~['isEmpty', 'isNotEmpty'].indexOf(type)) {
                if (type == 'isEmpty') {
                    data = {
                        type: 'or',
                        value: [
                            {
                                type: 'isNull',
                                field: this.name,
                            },
                            {
                                type: 'equals',
                                field: this.name,
                                value: ''
                            }
                        ],
                        data: {
                            type: type
                        }
                    }
                } else {
                    data = {
                        type: 'and',
                        value: [
                            {
                                type: 'notEquals',
                                field: this.name,
                                value: ''
                            },
                            {
                                type: 'isNotNull',
                                field: this.name,
                                value: null
                            }
                        ],
                        data: {
                            type: type
                        }
                    }
                }
                return data;
            } else {
                var value = this.$element.val().toString().trim();
                value = value.trim();
                if (value) {
                    data = {
                        value: value,
                        type: type
                    }
                    return data;
                }
            }
            return false;
        },

        getSearchType: function () {
            return this.getSearchParamsData().type || this.searchParams.typeFront || this.searchParams.type;
        },

        mailTo: function (emailAddress) {
            var attributes = {
                status: 'Draft',
                to: emailAddress
            };

            if (
                this.getConfig().get('emailForceUseExternalClient') ||
                this.getPreferences().get('emailUseExternalClient') ||
                !this.getAcl().checkScope('Email', 'create')
            ) {
                require('email-helper', function (EmailHelper) {
                    var emailHelper = new EmailHelper();
                    var link = emailHelper.composeMailToLink(attributes, this.getConfig().get('outboundEmailBccAddress'));
                    document.location.href = link;
                }.bind(this));

                return;
            }

            var viewName = this.getMetadata().get('clientDefs.' + this.scope + '.modalViews.compose') || 'views/modals/compose-email';

            this.notify('Loading...');
            this.createView('quickCreate', viewName, {
                attributes: attributes,
            }, function (view) {
                view.render();
                view.notify(false);
            });
        },

    });
});
