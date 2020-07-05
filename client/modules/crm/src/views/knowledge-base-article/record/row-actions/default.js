

Espo.define('crm:views/knowledge-base-article/record/row-actions/default', 'views/record/row-actions/default', function (Dep) {

    return Dep.extend({

        getActionList: function () {
            var actionList = Dep.prototype.getActionList.call(this);

            if (this.options.acl.edit && this.model.collection && this.model.collection.orderBy == 'order' && this.model.collection.order === 'asc') {
                actionList.push({
                    action: 'moveToTop',
                    label: 'Move to Top',
                    data: {
                        id: this.model.id
                    }
                });
                actionList.push({
                    action: 'moveUp',
                    label: 'Move Up',
                    data: {
                        id: this.model.id
                    }
                });
                actionList.push({
                    action: 'moveDown',
                    label: 'Move Down',
                    data: {
                        id: this.model.id
                    }
                });
                actionList.push({
                    action: 'moveToBottom',
                    label: 'Move to Bottom',
                    data: {
                        id: this.model.id
                    }
                });
            }

            return actionList;
        }
    });

});
