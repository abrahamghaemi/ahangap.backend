

define('views/modals/followers-list', ['views/modals/related-list'], function (Dep) {

    return Dep.extend({

        massActionRemoveDisabled: true,

        massActionMassUpdateDisabled: true,

        setup: function () {
            if (!this.getUser().isAdmin()) {
                this.unlinkDisabled = true;
            }

            Dep.prototype.setup.call(this);
        },

        actionSelectRelated: function () {
            var p = this.getParentView();
            var view = null;
            while (p) {
                if (p.actionSelectRelated) {
                    view = p;
                    break;
                }
                p = p.getParentView();
            }

            p.actionSelectRelated({
                link: this.link,
                primaryFilterName: 'active',
                massSelect: false,
                foreignEntityType: 'User'
            });
        }

    });
});
