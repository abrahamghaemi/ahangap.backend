

Espo.define('views/admin/template-manager/index', 'view', function (Dep) {

    return Dep.extend({

        template: 'admin/template-manager/index',


        data: function () {
            return {
                templateDataList: this.templateDataList
            };
        },

        events: {
            'click [data-action="selectTemplate"]': function (e) {
                var name = $(e.currentTarget).data('name');
                this.getRouter().checkConfirmLeaveOut(function () {
                    this.selectTemplate(name);
                }, this);
            }
        },

        setup: function () {
            this.templateDataList = [];

            var templateList = Object.keys(this.getMetadata().get(['app', 'templates']) || {});
            templateList.sort(function (v1, v2) {
                return this.translate(v1, 'templates', 'Admin').localeCompare(this.translate(v2, 'templates', 'Admin'));
            }.bind(this));

            templateList.forEach(function (template) {
                var defs = this.getMetadata().get(['app', 'templates', template]);
                if (defs.scopeListConfigParam || defs.scopeList) {
                    var scopeList = Espo.Utils.clone(defs.scopeList || this.getConfig().get(defs.scopeListConfigParam) || []);
                    scopeList.sort(function (v1, v2) {
                        return this.translate(v1, 'scopeNames').localeCompare(this.translate(v2, 'scopeNames'));
                    }.bind(this));
                    scopeList.forEach(function (scope) {
                        var o = {
                            name: template + '_' + scope,
                            text: this.translate(template, 'templates', 'Admin') + ' :: ' + this.translate(scope, 'scopeNames')
                        };
                        this.templateDataList.push(o);
                    }, this);
                    return;
                }

                var o = {
                    name: template,
                    text: this.translate(template, 'templates', 'Admin')
                };
                this.templateDataList.push(o);
            }, this);

            this.selectedTemplate = this.options.name;

            if (this.selectedTemplate) {
                this.once('after:render', function () {
                    this.selectTemplate(this.selectedTemplate, true);
                }, this);
            }
        },

        selectTemplate: function (name) {
            this.selectedTemplate = name;

            this.getRouter().navigate('#Admin/templateManager/name=' + this.selectedTemplate, {trigger: false});

            this.createRecordView();

            this.$el.find('[data-action="selectTemplate"]').removeClass('disabled').removeAttr('disabled');
            this.$el.find('[data-name="'+name+'"][data-action="selectTemplate"]').addClass('disabled').attr('disabled', 'disabled');
        },

        createRecordView: function () {
            Espo.Ui.notify(this.translate('loading', 'messages'));

            this.createView('record', 'views/admin/template-manager/edit', {
                el: this.getSelector() + ' .template-record',
                name: this.selectedTemplate
            }, function (view) {
                view.render();
                Espo.Ui.notify(false);
                $(window).scrollTop(0);
            }, this);
        },

        updatePageTitle: function () {
            this.setPageTitle(this.getLanguage().translate('Template Manager', 'labels', 'Admin'));
        },
    });
});
