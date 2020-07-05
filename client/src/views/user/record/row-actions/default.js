

Espo.define('views/user/record/row-actions/default', 'views/record/row-actions/default', function (Dep) {

    return Dep.extend({

        getActionList: function () {
            var scope = 'User';
            if (this.model.isPortal()) {
                scope = 'PortalUser';
            } else if (this.model.isApi()) {
                scope = 'ApiUser';
            }

            var list = [{
                action: 'quickView',
                label: 'View',
                data: {
                    id: this.model.id,
                    scope: scope
                },
                link: '#' + scope + '/view/' + this.model.id
            }];
            if (this.options.acl.edit) {
                list.push({
                    action: 'quickEdit',
                    label: 'Edit',
                    data: {
                        id: this.model.id,
                        scope: scope
                    },
                    link: '#' + scope + '/edit/' + this.model.id
                });
            }
            return list;
        }
    });

});
