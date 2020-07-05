

Espo.define('views/modals/image-preview', ['views/modal', 'lib!exif'], function (Dep) {

    return Dep.extend({

        cssName: 'image-preview',

        template: 'modals/image-preview',

        size: '',

        backdrop: true,

        transformClassList: [
            'transform-flip',
            'transform-rotate-180',
            'transform-flip-and-rotate-180',
            'transform-flip-and-rotate-270',
            'transform-rotate-90',
            'transform-flip-and-rotate-90',
            'transform-rotate-270',
        ],

        data: function () {
            return {
                name: this.options.name,
                url: this.getImageUrl(),
                originalUrl: this.getOriginalImageUrl(),
                size: this.size
            };
        },

        setup: function () {
            this.buttonList = [];
            this.headerHtml = '&nbsp;';

            this.navigationEnabled = (this.options.imageList && this.options.imageList.length > 1);

            this.imageList = this.options.imageList || [];

            this.once('remove', function () {
                $(window).off('resize.image-review');
            }, this);
        },

        getImageUrl: function () {
            var url = this.getBasePath() + '?entryPoint=image&id=' + this.options.id;
            if (this.size) {
                url += '&size=' + this.size;
            }
            if (this.getUser().get('portalId')) {
                url += '&portalId=' + this.getUser().get('portalId');
            }
            return url;
        },

        getOriginalImageUrl: function () {
            var url = this.getBasePath() + '?entryPoint=image&id=' + this.options.id;
            if (this.getUser().get('portalId')) {
                url += '&portalId=' + this.getUser().get('portalId');
            }
            return url;
        },

        onImageLoad: function () {
            console.log(1);
        },

        afterRender: function () {
            $container = this.$el.find('.image-container');
            $img = this.$img = this.$el.find('.image-container img');

            $img.on('load', function () {
                var self = this;
                EXIF.getData($img.get(0), function () {
                    var orientation = EXIF.getTag(this, 'Orientation');
                    switch (orientation) {
                        case 2:
                            $img.addClass('transform-flip');
                            break;
                        case 3:
                            $img.addClass('transform-rotate-180');
                            break;
                        case 4:
                            $img.addClass('transform-rotate-180');
                            $img.addClass('transform-flip');
                            break;
                        case 5:
                            $img.addClass('transform-rotate-270');
                            $img.addClass('transform-flip');
                            break;
                        case 6:
                            $img.addClass('transform-rotate-90');
                            break;
                        case 7:
                            $img.addClass('transform-rotate-90');
                            $img.addClass('transform-flip');
                            break;
                        case 8:
                            $img.addClass('transform-rotate-270');
                            break;
                    }
                });
            }.bind(this));

            if (this.navigationEnabled) {
                $img.css('cursor', 'pointer');
                $img.click(function () {
                    this.switchToNext();
                }.bind(this));
            }

            var manageSize = function () {
                var width = $container.width();
                $img.css('maxWidth', width);
            }.bind(this);

            $(window).off('resize.image-review');
            $(window).on('resize.image-review', function () {
                manageSize();
            });

            setTimeout(function () {
                manageSize();
            }, 100);
        },

        switchToNext: function () {

            this.transformClassList.forEach(function (item) {
                this.$img.removeClass(item);
            }, this);

            var index = -1;
            this.imageList.forEach(function (d, i) {
                if (d.id === this.options.id) {
                    index = i;
                }
            }, this);

            index++;
            if (index > this.imageList.length - 1) {
                index = 0;
            }

            this.options.id = this.imageList[index].id
            this.options.name = this.imageList[index].name;
            this.reRender();
        },

    });
});
