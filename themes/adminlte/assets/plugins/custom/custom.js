$(document).ready(function() {
    $('.tip').tooltip();
    $(document).on('click', '.activate_toggle_po', function(e) {
        e.preventDefault();
        $('.activate_toggle_po').popover({html: true, placement: 'left', trigger: 'manual'}).popover('show').not(this).popover('hide');
        return false;
    });
    $(document).on('click', '.activate_toggle_po-close', function() {
        $('.activate_toggle_po').popover('hide');
        return false;
    });
});



(function() {
  "use strict";
  var EkkoLightbox;

  EkkoLightbox = function(element, options) {
    var content, footer, header, video_id,
      _this = this;
    this.options = $.extend({
      gallery_parent_selector: '*:not(.row)',
      title: null,
      footer: null,
      remote: null,
      left_arrow_class: '.fa .fa-chevron-left',
      right_arrow_class: '.fa .fa-chevron-right',
      directional_arrows: true,
      type: null,
      onShow: function() {},
      onShown: function() {},
      onHide: function() {},
      onHidden: function() {},
      id: false
    }, options || {});
    this.$element = $(element);
    content = '';
    this.modal_id = this.options.modal_id ? this.options.modal_id : 'ekkoLightbox-' + Math.floor((Math.random() * 1000) + 1);
    header = '<div class="modal-header"' + (this.options.title ? '' : ' style="display:none"') + '><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title">' + this.options.title + '</h4></div>';
    footer = '<div class="modal-footer"' + (this.options.footer ? '' : ' style="display:none"') + '>' + this.options.footer + '</div>';
    $(document.body).append('<div id="' + this.modal_id + '" class="ekko-lightbox modal fade" tabindex="-1"><div class="modal-dialog"><div class="modal-content">' + header + '<div class="modal-body"><div class="ekko-lightbox-container"><div></div></div></div>' + footer + '</div></div></div>');
    this.modal = $('#' + this.modal_id);
    this.modal_body = this.modal.find('.modal-body').first();
    this.lightbox_container = this.modal_body.find('.ekko-lightbox-container').first();
    this.lightbox_body = this.lightbox_container.find('> div:first-child').first();
    this.modal_arrows = null;
    this.padding = {
      left: parseFloat(this.modal_body.css('padding-left'), 10),
      right: parseFloat(this.modal_body.css('padding-right'), 10),
      bottom: parseFloat(this.modal_body.css('padding-bottom'), 10),
      top: parseFloat(this.modal_body.css('padding-top'), 10)
    };
    if (!this.options.remote) {
      this.error('No remote target given');
    } else {
      this.gallery = this.$element.data('gallery');
      if (this.gallery) {
        if (this.options.gallery_parent_selector === 'document.body' || this.options.gallery_parent_selector === '') {
          this.gallery_items = $(document.body).find('*[data-toggle="lightbox"][data-gallery="' + this.gallery + '"]');
        } else {
          this.gallery_items = this.$element.parents(this.options.gallery_parent_selector).first().find('*[data-toggle="lightbox"][data-gallery="' + this.gallery + '"]');
        }
        this.gallery_index = this.gallery_items.index(this.$element);
        $(document).on('keydown.ekkoLightbox', this.navigate.bind(this));
        if (this.options.directional_arrows && this.gallery_items.length > 1) {
          this.lightbox_container.prepend('<div class="ekko-lightbox-nav-overlay"><a href="#" class="' + this.strip_stops(this.options.left_arrow_class) + '"></a><a href="#" class="' + this.strip_stops(this.options.right_arrow_class) + '"></a></div>');
          this.modal_arrows = this.lightbox_container.find('div.ekko-lightbox-nav-overlay').first();
          this.lightbox_container.find('a' + this.strip_spaces(this.options.left_arrow_class)).on('click', function(event) {
            event.preventDefault();
            return _this.navigate_left();
          });
          this.lightbox_container.find('a' + this.strip_spaces(this.options.right_arrow_class)).on('click', function(event) {
            event.preventDefault();
            return _this.navigate_right();
          });
        }
      }
      if (this.options.type) {
        if (this.options.type === 'image') {
          this.preloadImage(this.options.remote, true);
        } else if (this.options.type === 'youtube' && (video_id = this.getYoutubeId(this.options.remote))) {
          this.showYoutubeVideo(video_id);
        } else if (this.options.type === 'vimeo') {
          this.showVimeoVideo(this.options.remote);
        } else {
          this.error("Could not detect remote target type. Force the type using data-type=\"image|youtube|vimeo\"");
        }
      } else {
        this.detectRemoteType(this.options.remote);
      }
    }
    this.modal.on('show.bs.modal', this.options.onShow.bind(this)).on('shown.bs.modal', function() {
      if (_this.modal_arrows) {
        _this.resize(_this.lightbox_body.width());
      }
      return _this.options.onShown.call(_this);
    }).on('hide.bs.modal', this.options.onHide.bind(this)).on('hidden.bs.modal', function() {
      if (_this.gallery) {
        $(document).off('keydown.ekkoLightbox');
      }
      _this.modal.remove();
      return _this.options.onHidden.call(_this);
    }).modal('show', options);
    return this.modal;
  };

  EkkoLightbox.prototype = {
    strip_stops: function(str) {
      return str.replace(/\./g, '');
    },
    strip_spaces: function(str) {
      return str.replace(/\s/g, '');
    },
    isImage: function(str) {
      return str.match(/(^data:image\/.*,)|(\.(jp(e|g|eg)|gif|png|bmp|webp|svg)((\?|#).*)?$)/i);
    },
    isSwf: function(str) {
      return str.match(/\.(swf)((\?|#).*)?$/i);
    },
    getYoutubeId: function(str) {
      var match;
      match = str.match(/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/);
      if (match && match[2].length === 11) {
        return match[2];
      } else {
        return false;
      }
    },
    getVimeoId: function(str) {
      if (str.indexOf('vimeo') > 0) {
        return str;
      } else {
        return false;
      }
    },
    navigate: function(event) {
      event = event || window.event;
      if (event.keyCode === 39 || event.keyCode === 37) {
        if (event.keyCode === 39) {
          return this.navigate_right();
        } else if (event.keyCode === 37) {
          return this.navigate_left();
        }
      }
    },
    navigate_left: function() {
      var src;
      if (this.gallery_items.length === 1) {
        return;
      }
      if (this.gallery_index === 0) {
        this.gallery_index = this.gallery_items.length - 1;
      } else {
        this.gallery_index--;
      }
      this.$element = $(this.gallery_items.get(this.gallery_index));
      this.updateTitleAndFooter();
      src = this.$element.attr('data-remote') || this.$element.attr('href');
      return this.detectRemoteType(src, this.$element.attr('data-type'));
    },
    navigate_right: function() {
      var next, src;
      if (this.gallery_items.length === 1) {
        return;
      }
      if (this.gallery_index === this.gallery_items.length - 1) {
        this.gallery_index = 0;
      } else {
        this.gallery_index++;
      }
      this.$element = $(this.gallery_items.get(this.gallery_index));
      src = this.$element.attr('data-remote') || this.$element.attr('href');
      this.updateTitleAndFooter();
      this.detectRemoteType(src, this.$element.attr('data-type'));
      if (this.gallery_index + 1 < this.gallery_items.length) {
        next = $(this.gallery_items.get(this.gallery_index + 1), false);
        src = next.attr('data-remote') || next.attr('href');
        if (next.attr('data-type') === 'image' || this.isImage(src)) {
          return this.preloadImage(src, false);
        }
      }
    },
    detectRemoteType: function(src, type) {
      var video_id;
      if (type === 'image' || this.isImage(src)) {
        return this.preloadImage(src, true);
      } else if (type === 'youtube' || (video_id = this.getYoutubeId(src))) {
        return this.showYoutubeVideo(video_id);
      } else if (type === 'vimeo' || (video_id = this.getVimeoId(src))) {
        return this.showVimeoVideo(video_id);
      } else {
        return this.error("Could not detect remote target type. Force the type using data-type=\"image|youtube|vimeo\"");
      }
    },
    updateTitleAndFooter: function() {
      var caption, footer, header, title;
      header = this.modal.find('.modal-dialog .modal-content .modal-header');
      footer = this.modal.find('.modal-dialog .modal-content .modal-footer');
      title = this.$element.data('title') || "";
      caption = this.$element.data('footer') || "";
      if (title) {
        header.css('display', '').find('.modal-title').html(title);
      } else {
        header.css('display', 'none');
      }
      if (caption) {
        footer.css('display', '').html(caption);
      } else {
        footer.css('display', 'none');
      }
      return this;
    },
    showLoading: function() {
      this.lightbox_body.html('<div class="modal-loading">Loading..</div>');
      return this;
    },
    showYoutubeVideo: function(id) {
      var height, width;
      width = this.$element.data('width') || 560;
      height = this.$element.data('height') || 315;
      this.resize(width);
      this.lightbox_body.html('<iframe width="' + width + '" height="' + height + '" src="//www.youtube.com/embed/' + id + '?badge=0&autoplay=1&html5=1" frameborder="0" allowfullscreen></iframe>');
      if (this.modal_arrows) {
        return this.modal_arrows.css('display', 'none');
      }
    },
    showVimeoVideo: function(id) {
      this.resize(500);
      this.lightbox_body.html('<iframe width="500" height="281" src="' + id + '?autoplay=1" frameborder="0" allowfullscreen></iframe>');
      if (this.modal_arrows) {
        return this.modal_arrows.css('display', 'none');
      }
    },
    error: function(message) {
      this.lightbox_body.html(message);
      return this;
    },
    preloadImage: function(src, onLoadShowImage) {
      var img,
        _this = this;
      img = new Image();
      if ((onLoadShowImage == null) || onLoadShowImage === true) {
        img.onload = function() {
          var image, width;
          width = _this.checkImageDimensions(img.width);
          image = $('<img />');
          image.attr('src', img.src);
          image.css('max-width', '100%');
          _this.lightbox_body.html(image);
          if (_this.modal_arrows) {
            _this.modal_arrows.css('display', 'block');
          }
          return _this.resize(width);
        };
        img.onerror = function() {
          return _this.error('Failed to load image: ' + src);
        };
      }
      img.src = src;
      return img;
    },
    resize: function(width) {
      var width_inc_padding;
      width_inc_padding = width + this.padding.left + this.padding.right;
      this.modal.find('.modal-content').css('width', width_inc_padding);
      this.modal.find('.modal-dialog').css('width', width_inc_padding + 20);
      this.lightbox_container.find('a').css('padding-top', function() {
        return $(this).parent().height() / 2;
      });
      return this;
    },
    checkImageDimensions: function(max_width) {
      var w, width;
      w = $(window);
      width = max_width;
      if ((max_width + (this.padding.left + this.padding.right + 20)) > w.width()) {
        width = w.width() - (this.padding.left + this.padding.right + 20);
      }
      return width;
    },
    close: function() {
      return this.modal.modal('hide');
    }
  };

  $.fn.ekkoLightbox = function(options) {
    return this.each(function() {
      var $this;
      $this = $(this);
      options = $.extend({
        remote: $this.attr('data-remote') || $this.attr('href'),
        gallery_parent_selector: $this.attr('data-parent'),
        type: $this.attr('data-type')
      }, options, $this.data());
      new EkkoLightbox(this, options);
      return this;
    });
  };

}).call(this);




! function() {
    "use strict";
    $(document).ready(function() {
        function e(e) {
            $(e.target).prev(".panel-heading").find("i.indicator").toggleClass("fa-chevron-up fa-chevron-down")
        }

        function t(e) {
            $(e.target).prev(".accordion-faq-toggle").find("i.indicator").toggleClass("fa-chevron-circle-up fa-chevron-circle-down")
        }

        function o(e) {
            var t = null,
                a = null,
                o = $(e),
                i = $(".dropdown-menu", e),
                n = o.parents("ul.nav");
            return n.size() > 0 && (t = n.data("dropdown-in") || null, a = n.data("dropdown-out") || null), {
                target: e,
                dropdown: o,
                dropdownMenu: i,
                effectIn: i.data("dropdown-in") || t,
                effectOut: i.data("dropdown-out") || a
            }
        }

        function i(e, t) {
            t && (e.dropdown.addClass("dropdown-animating"), e.dropdownMenu.addClass("animated"), e.dropdownMenu.addClass(t))
        }

        function n(e, t) {
            var a = "webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend";
            e.dropdown.one(a, function() {
                e.dropdown.removeClass("dropdown-animating"), e.dropdownMenu.removeClass("animated"), e.dropdownMenu.removeClass(e.effectIn), e.dropdownMenu.removeClass(e.effectOut), "function" == typeof t && t()
            })
        }
        $("#side-menu").length && $("#side-menu").metisMenu({
            doubleTapToGo: !1
        }), $(".oaken-clear").hide(), $(".oakenwidgets").length && ($(".oakenwidgets").oakenwidgets(), $(".oaken-clear").show()), $("#breadcrumbs-toggle").click(function(e) {
            e.preventDefault(), $(".breadcrumb").toggleClass("hide")
        }), $("#navbar-inverse-toggle").click(function(e) {
            e.preventDefault(), $(".main-top-navbar").toggleClass("navbar-inverse")
        }), $("#enable-userbar-material-toggle").click(function(e) {
            e.preventDefault(), $(".material-button-anim").toggleClass("show"), $(".btn.userbar-toggle").toggleClass("hide"), $(".userbar-wrapper").toggleClass("enable-material"), $(".userbar-wrapper .nav-tabs").toggleClass("hide")
        }), $("#load-yellow-styles").click(function(e) {
            e.preventDefault(), $(document.body).attr("class", "yellow-body")
        }), $("#load-green-styles").click(function(e) {
            e.preventDefault(), $(document.body).attr("class", "green-body")
        }), $("#load-blue-styles").click(function(e) {
            e.preventDefault(), $(document.body).attr("class", "blue-body")
        }), $("#load-khaki-styles").click(function(e) {
            e.preventDefault(), $(document.body).attr("class", "khaki-body")
        }), $("#load-pink-styles").click(function(e) {
            e.preventDefault(), $(document.body).attr("class", "pink-body")
        }), $("#load-default-styles").click(function(e) {
            e.preventDefault(), $(document.body).removeClass()
        }), $(".material-button-toggle").click(function() {
            $(this).toggleClass("open"), $(".option").toggleClass("scale-on"), $(".material-button-anim").toggleClass("open")
        }), $(".scrollToTop").click(function() {
            return $("html, body").animate({
                scrollTop: 0
            }, 800), !1
        }), $.fn.clickOutsideThisElement = function(e) {
            return this.each(function() {
                var t = this;
                $(document).on("click touchstart", function(a) {
                    $(a.target).closest(t).length || e.call(t, a)
                })
            })
        }, $(".userbar-wrapper").clickOutsideThisElement(function() {
            $("body").removeClass("userbar-wrapper-opened"), $(".option").removeClass("scale-on"), $(".material-button-toggle").removeClass("open")
        }), $(".sidebar-toggle").click(function(e) {
            $("body").toggleClass("sidebar-wrapper-closed")
        }), $(".sidebar-toggle-xs").click(function(e) {
            $("body").toggleClass("sidebar-wrapper-open-xs"), e.stopPropagation()
        }), $(".userbar-toggle").click(function(e) {
            $("body").toggleClass("userbar-wrapper-opened"), e.stopPropagation()
        }), $(".tooltiped").length && $(".tooltiped").tooltip(), $(".popovered").length && $(".popovered").popover({
            html: "true"
        }), $(".popover-hovered").length && $(".popover-hovered").popover({
            html: "true",
            trigger: "hover"
        }), $('[data-tooltip="tooltip"]').tooltip(), $("#accordion").on("hidden.bs.collapse", e), $("#accordion").on("shown.bs.collapse", e), $("#accordion-faq").on("hidden.bs.collapse", t), $("#accordion-faq").on("shown.bs.collapse", t), $(".megamenu-hover .dropdown-toggle").length && $(".megamenu-hover .dropdown-toggle").dropdownHover({
            delay: 50,
            hoverDelay: 50
        }), $(".keep_open").click(function(e) {
            e.stopPropagation()
        }), $(".dropdown").on("show.bs.dropdown", function() {
            var e = $(this);
            setTimeout(function() {
                var t = $(".carousel", e).carousel();
                $("[data-slide], [data-slide-to]", t).click(function(e) {
                    e.preventDefault(), $(this).trigger("click.bs.carousel.data-api")
                })
            }, 10)
        }), $('.dropdown-menu a[data-toggle="tab"]').click(function(e) {
            e.stopPropagation(), $(this).tab("show")
        });
        var a = $(".dropdown, .dropup");
        if (a.on({
                "show.bs.dropdown": function() {
                    var e = o(this);
                    i(e, e.effectIn)
                },
                "shown.bs.dropdown": function() {
                    var e = o(this);
                    e.effectIn && e.effectOut && n(e, function() {})
                },
                "hide.bs.dropdown": function(e) {
                    var t = o(this);
                    t.effectOut && (e.preventDefault(), i(t, t.effectOut), n(t, function() {
                        t.dropdown.removeClass("open")
                    }))
                }
            }), $(".lockme").click(function(e) {
                e.preventDefault(), $("#lockscreen").modal(), $("#yesilock").click(function() {
                    window.open("pages-lock.html", "_self"), $("#lockscreen").modal("hide")
                })
            }), $(".goaway").click(function(e) {
                e.preventDefault(), $("#signout").modal(), $("#yesigo").click(function() {
                    window.open("pages-login.html", "_self"), $("#signout").modal("hide")
                })
            }), $(document).delegate('*[data-toggle="lightbox"]', "click", function(e) {
                return e.preventDefault(), $(this).ekkoLightbox({
                    always_show_close: !0
                })
            }), $(document).on("change", ".btn-file :file", function() {
                var e = $(this),
                    t = e.get(0).files ? e.get(0).files.length : 1,
                    a = e.val().replace(/\\/g, "/").replace(/[\w\W]*\//, "");
                e.trigger("fileselect", [t, a])
            }), $(".btn-file :file").on("fileselect", function(e, t, a) {
                var o = $(this).parents(".input-group").find(":text"),
                    i = t > 1 ? t + " files selected" : a;
                o.length ? o.val(i) : i && alert(i)
            }), $(".next").on("click", function() {
                var e = $(this).data("currentBlock"),
                    t = $(this).data("nextBlock");
                t > e && !1 === $("#basic-wizard").parsley().validate("block" + e) || ($(".block" + e).removeClass("show").addClass("hidden"), $(".block" + t).removeClass("hidden").addClass("show"))
            }), $("#rootwizard-pills").length && $("#rootwizard-pills").bootstrapWizard({
                onTabShow: function(e, t, a) {
                    var o = t.find("li").length,
                        i = a + 1,
                        n = i / o * 100;
                    $("#rootwizard-pills").find("#rootwizard-pills-progress").css({
                        width: n + "%"
                    })
                }
            }), $("#rootwizard-navs").length && $("#rootwizard-navs").bootstrapWizard({
                onTabShow: function(e, t, a) {
                    var o = t.find("li").length,
                        i = a + 1,
                        n = i / o * 100;
                    $("#rootwizard-navs").find("#progressbar-navs").css({
                        width: n + "%"
                    })
                }
            }), $("#rootwizard").length && $("#rootwizard").bootstrapWizard({
                onTabShow: function(e, t, a) {
                    var o = t.find("li").length,
                        i = a + 1,
                        n = i / o * 100;
                    $("#rootwizard").find("#progressbar").css({
                        width: n + "%"
                    }), $("#rootwizard").find(".last").toggle(i >= o), $("#rootwizard").find(".next").toggle(o > i)
                }
            }), $("#rootwizard-2").length && $("#rootwizard-2").bootstrapWizard({
                onTabShow: function(e, t, a) {
                    var o = t.find("li").length,
                        i = a + 1;
                    $("#rootwizard-2").find(".last").toggle(i >= o), $("#rootwizard-2").find(".next").toggle(o > i)
                }
            }), $("#rootwizard, #rootwizard-2").on("show.bs.tab", function(e) {
                var t = $(this),
                    a = $("ul.nav", t),
                    o = a.children().index($(e.target).parent()) + 1,
                    i = a.children().index($(e.relatedTarget).parent()) + 1;
                if (o > i) {
                    if (o - i > 1) return !1;
                    if (!$("form", t).parsley().validate("tab-" + i)) return $(e.relatedTarget).parent().removeClass("validated-tab"), !1;
                    $(e.relatedTarget).parent().addClass("validated-tab")
                }
            }), $("#summernote").length && $("#summernote").summernote({
                height: 300
            }), $("#wysihtml5").length && $("#wysihtml5").wysihtml5({
                toolbar: {
                    fa: !0,
                    size: "sm"
                }
            }), $("#wysihtml5-inbox-modal").length && $("#wysihtml5-inbox-modal").wysihtml5({
                toolbar: {
                    "font-styles": !1,
                    fa: !0,
                    size: "sm"
                }
            }), $("#knob1").length && $("#knob1").knob({
                height: "130"
            }), $("#knob2").length && $("#knob2").knob({
                height: "130",
                font: "Montserrat, Roboto, sans-serif"
            }), $("#knob3").length && $("#knob3").knob({
                height: "130",
                font: "Montserrat, Roboto, sans-serif"
            }), $("#knob4").length && $("#knob4").knob({
                height: "130",
                font: "Montserrat, Roboto, sans-serif"
            }), $("#knob5").length && $("#knob5").knob({
                height: "130",
                font: "Montserrat, Roboto, sans-serif"
            }), $("#knob6").length && $("#knob6").knob({
                height: "130",
                font: "Montserrat, Roboto, sans-serif"
            }), $("#s,#s3,#s4,#s5,#s6").length && $("#s,#s3,#s4,#s5,#s6").stepper(), $("#s2").length && $("#s2").stepper({
                wheel_step: 1,
                arrow_step: .2
            }), $("#userbar-calendar").length && $("#userbar-calendar").datepicker({
                dateFormat: "dd.mm.yy",
                prevText: '<i class="fa fa-chevron-left"></i>',
                nextText: '<i class="fa fa-chevron-right"></i>'
            }), $("#widget-calendar").length && $("#widget-calendar").datepicker({
                dateFormat: "dd.mm.yy",
                prevText: '<i class="fa fa-chevron-left"></i>',
                nextText: '<i class="fa fa-chevron-right"></i>'
            }), $("#datepicker-1").length && $("#datepicker-1").datepicker({
                prevText: '<i class="fa fa-chevron-left"></i>',
                nextText: '<i class="fa fa-chevron-right"></i>'
            }), $("#datepicker-2").length && $("#datepicker-2").datepicker({
                prevText: '<i class="fa fa-chevron-left"></i>',
                nextText: '<i class="fa fa-chevron-right"></i>',
                changeYear: !0
            }), $("#datepicker-3").length && $("#datepicker-3").datepicker({
                prevText: '<i class="fa fa-chevron-left"></i>',
                nextText: '<i class="fa fa-chevron-right"></i>'
            }), $("#datepicker-3").length && $("#datepicker-4").datepicker({
                prevText: '<i class="fa fa-chevron-left"></i>',
                nextText: '<i class="fa fa-chevron-right"></i>',
                changeMonth: !0,
                changeYear: !0
            }), $("#from").length && $("#from").datepicker({
                prevText: '<i class="fa fa-chevron-left"></i>',
                nextText: '<i class="fa fa-chevron-right"></i>',
                defaultDate: "+1w",
                changeMonth: !1,
                numberOfMonths: 2,
                onClose: function(e) {
                    $("#to").datepicker("option", "minDate", e)
                }
            }), $("#to").length && $("#to").datepicker({
                prevText: '<i class="fa fa-chevron-left"></i>',
                nextText: '<i class="fa fa-chevron-right"></i>',
                defaultDate: "+1w",
                changeMonth: !0,
                numberOfMonths: 2,
                onClose: function(e) {
                    $("#from").datepicker("option", "maxDate", e)
                }
            }), $("#datetimepicker-1").length && $("#datetimepicker-1").datetimepicker({
                prevText: '<i class="fa fa-chevron-left"></i>',
                nextText: '<i class="fa chevron-right"></i>'
            }), $("#datetimepicker-2").length && $("#datetimepicker-2").timepicker(), $("#datetimepicker-3").length && $("#datetimepicker-3").datetimepicker({
                prevText: '<i class="fa fa-chevron-left"></i>',
                nextText: '<i class="fa fa-chevron-right"></i>',
                timeFormat: "HH:mm z",
                timezone: "MT",
                timezoneList: [{
                    value: "ET",
                    label: "Eastern"
                }, {
                    value: "CT",
                    label: "Central"
                }, {
                    value: "MT",
                    label: "Mountain"
                }, {
                    value: "PT",
                    label: "Pacific"
                }]
            }), $("#datetimepicker-4").length && $("#datetimepicker-4").timepicker({
                prevText: '<i class="fa chevron-left"></i>',
                nextText: '<i class="fa chevron-right"></i>',
                timeFormat: "HH:mm:ss",
                stepHour: 2,
                stepMinute: 10,
                stepSecond: 10
            }), $("#datetimepicker-5").length && $("#datetimepicker-5").datetimepicker({
                altField: "#datetimepicker-5-alt",
                prevText: '<i class="fa fa-chevron-left"></i>',
                nextText: '<i class="fa fa-chevron-right"></i>'
            }), $("#month-picker-1").length && $("#month-picker-1").monthpicker({
                changeYear: !1,
                stepYears: 1,
                prevText: '<i class="fa fa-angle-left"></i>',
                nextText: '<i class="fa fa-angle-right"></i>',
                showButtonPanel: !0
            }), $("#month-picker-2").length && $("#month-picker-2").monthpicker({
                changeYear: !0,
                stepYears: 1,
                prevText: '<i class="fa fa-angle-left"></i>',
                nextText: '<i class="fa fa-angle-right"></i>',
                showButtonPanel: !0
            }), $.minicolors = {
                defaults: {
                    animationSpeed: 50,
                    animationEasing: "swing",
                    change: null,
                    changeDelay: 0,
                    control: "hue",
                    defaultValue: "",
                    hide: null,
                    hideSpeed: 100,
                    inline: !1,
                    letterCase: "lowercase",
                    opacity: !1,
                    position: "bottom left",
                    show: null,
                    showSpeed: 100,
                    theme: "bootstrap"
                }
            }, $("#hue-demo").length && $("#hue-demo").minicolors(), $("#saturation-demo").length && $("#saturation-demo").minicolors({
                control: "saturation"
            }), $("#brightness-demo").length && $("#brightness-demo").minicolors({
                control: "brightness"
            }), $("#wheel-demo").length && $("#wheel-demo").minicolors({
                control: "wheel"
            }), $("#opacity").length && $("#opacity").minicolors({
                opacity: ".6",
                format: "rgb"
            }), $("#letter-case").length && $("#letter-case").minicolors({
                letterCase: "uppercase"
            }), $("#ui-slider1").length && $("#ui-slider1").slider({
                min: 0,
                max: 500,
                slide: function(e, t) {
                    $("#ui-slider1-value").text(t.value)
                }
            }), $("#ui-slider2").length && $("#ui-slider2").slider({
                min: 0,
                max: 500,
                range: !0,
                values: [75, 300],
                slide: function(e, t) {
                    $("#ui-slider2-value1").text(t.values[0]), $("#ui-slider2-value2").text(t.values[1])
                }
            }), $("#ui-slider3").length && $("#ui-slider3").slider({
                min: 0,
                max: 500,
                step: 100,
                slide: function(e, t) {
                    $("#ui-slider3-value").text(t.value)
                }
            }), $("#shop-slider").length && ($("#shop-slider").slider({
                range: !0,
                min: 5,
                max: 500,
                values: [11, 100],
                slide: function(e, t) {
                    $("#amount").val("$" + t.values[0] + " - $" + t.values[1])
                }
            }), $("#amount").val("$" + $("#shop-slider").slider("values", 0) + " - $" + $("#shop-slider").slider("values", 1))), $("#list").click(function(e) {
                e.preventDefault(), $("#products .item").addClass("list-group-item")
            }), $("#grid").click(function(e) {
                e.preventDefault(), $("#products .item").removeClass("list-group-item"), $("#products .item").addClass("grid-group-item")
            }), $(".alert-autocloseable-success").hide(), $(".alert-autocloseable-warning").hide(), $(".alert-autocloseable-danger").hide(), $(".alert-autocloseable-info").hide(), $("#autoclosable-btn-success").click(function() {
                $("#autoclosable-btn-success").prop("disabled", !0), $(".alert-autocloseable-success").show(), $(".alert-autocloseable-success").delay(5e3).fadeOut("slow", function() {
                    $("#autoclosable-btn-success").prop("disabled", !1)
                })
            }), $("#autoclosable-btn-warning").click(function() {
                $("#autoclosable-btn-warning").prop("disabled", !0), $(".alert-autocloseable-warning").show(), $(".alert-autocloseable-warning").delay(3e3).fadeOut("slow", function() {
                    $("#autoclosable-btn-warning").prop("disabled", !1)
                })
            }), $("#autoclosable-btn-danger").click(function() {
                $("#autoclosable-btn-danger").prop("disabled", !0), $(".alert-autocloseable-danger").show(), $(".alert-autocloseable-danger").delay(5e3).fadeOut("slow", function() {
                    $("#autoclosable-btn-danger").prop("disabled", !1)
                })
            }), $("#autoclosable-btn-info").click(function() {
                $("#autoclosable-btn-info").prop("disabled", !0), $(".alert-autocloseable-info").show(), $(".alert-autocloseable-info").delay(6e3).fadeOut("slow", function() {
                    $("#autoclosable-btn-info").prop("disabled", !1)
                })
            }), $("#nestable").length && $("#nestable").nestable({
                group: 1
            }), $("#nestable2").length && $("#nestable2").nestable({
                group: 1
            }), $("#nestable-menu").length && $("#nestable-menu").on("click", function(e) {
                var t = $(e.target),
                    a = t.data("action");
                "expand-all" === a && $(".dd").nestable("expandAll"), "collapse-all" === a && $(".dd").nestable("collapseAll")
            }), $("#nestable3").length && $("#nestable3").nestable(), $("#nestable4").length && $("#nestable4").nestable(), $(".quotes-marquee").length && $(".quotes-marquee").marquee({
                duration: 15e3,
                gap: 50,
                delayBeforeStart: 0,
                direction: "left",
                duplicated: !0,
                pauseOnHover: !0
            }), $("#gallery").length && $("#gallery").imagesLoaded(function() {
                var e = $("#gallery").isotope({
                    masonry: {}
                });
                $("#filters").on("click", "button", function() {
                    var t = $(this).attr("data-filter");
                    e.isotope({
                        filter: t
                    })
                })
            }), document.getElementById("displayMoment")) {
            var l = moment(),
                s = document.getElementById("displayMoment");
            s.innerHTML = l.format("<p>D</p><p>MMMM</p> <p>dddd</p>")
        }
        $(".chat-toggler").click(function(e) {
            $(".chat-message-form").toggleClass("chat-message-form-toggle", "fast"), $(".chat-users-menu").toggleClass("chatbar-toggle", "fast"), $(".chat-messages").toggleClass("chat-messages-toggle", "fast"), $(".chat-header").toggleClass("chat-header-toggle", "fast")
        }), $(".chat-overlay-button").click(function(e) {
            e.preventDefault(), $(".chat-overlay").toggleClass("chat-closed chat-opened", "fast")
        }), $("#b1").bind("click mouseover", function() {
            $(".chat-app .chat-messages").css("background-image", 'url("images/backgrounds/1.png")')
        }), $("#b2").bind("click mouseover", function() {
            $(".chat-app .chat-messages").css("background-image", 'url("images/backgrounds/2.png")')
        }), $("#b3").bind("click mouseover", function() {
            $(".chat-app .chat-messages").css("background-image", 'url("images/backgrounds/3.png")')
        }), $("#b4").bind("click mouseover", function() {
            $(".chat-app .chat-messages").css("background-image", 'url("images/backgrounds/4.png")')
        }), $("#b5").bind("click mouseover", function() {
            $(".chat-app .chat-messages").css("background-image", 'url("images/backgrounds/5.png")')
        }), $(".piechart-1").length && $(".piechart-1").sparkline("html", {
            disableHiddenCheck: !0,
            defaultPixelsPerValue: 1,
            type: "line",
            width: "125",
            height: "40",
            lineColor: "#fff",
            fillColor: "#5fb6c7",
            lineWidth: 3,
            spotColor: "#ffffff",
            minSpotColor: "#000",
            maxSpotColor: "#000",
            highlightSpotColor: "#a6c172",
            spotRadius: 5,
            drawNormalOnTop: !1
        }), $(".piechart-2").length && $(".piechart-2").sparkline("html", {
            disableHiddenCheck: !0,
            type: "line",
            width: "125",
            height: "40",
            lineColor: "#9ab946",
            fillColor: !1,
            lineWidth: 5,
            spotColor: "#ffffff",
            minSpotColor: "#000",
            maxSpotColor: "#000",
            highlightSpotColor: "#a6c172",
            spotRadius: 3,
            drawNormalOnTop: !1
        }), $(".piechart-3").length && $(".piechart-3").sparkline("html", {
            disableHiddenCheck: !0,
            defaultPixelsPerValue: 1,
            type: "line",
            width: "125",
            height: "40",
            lineColor: "#fff",
            fillColor: "#5fb6c7",
            lineWidth: 5,
            spotColor: "#ffffff",
            minSpotColor: "#000",
            maxSpotColor: "#000",
            highlightSpotColor: "#a6c172",
            spotRadius: 3,
            drawNormalOnTop: !1
        }), $(".piechart-4").length && $(".piechart-4").sparkline("html", {
            disableHiddenCheck: !0,
            defaultPixelsPerValue: 1,
            type: "line",
            width: "125",
            height: "40",
            lineColor: "#fff",
            fillColor: "#5fb6c7",
            lineWidth: 5,
            spotColor: "#ffffff",
            minSpotColor: "#000",
            maxSpotColor: "#000",
            highlightSpotColor: "#a6c172",
            spotRadius: 3,
            drawNormalOnTop: !1
        }), $(".piechart-5").length && $(".piechart-5").sparkline("html", {
            disableHiddenCheck: !0,
            type: "pie",
            width: "40",
            height: "40",
            sliceColors: ["#fff", "#9ab946", "#000", "#109618", "#a4b7bf", "#506066", "#667880", "#8ca0a8"]
        }), $(".piechart-6").length && $(".piechart-6").sparkline("html", {
            disableHiddenCheck: !0,
            type: "pie",
            width: "40",
            height: "40",
            sliceColors: ["#fff", "#9ab946", "#f87aa0", "#109618", "#a4b7bf", "#506066", "#667880", "#8ca0a8"]
        }), $(function(e) {
            $(".transition-timer-carousel").on("slide.bs.carousel", function(e) {
                $(".transition-timer-carousel-progress-bar", this).removeClass("animate").css("width", "0%")
            }).on("slid.bs.carousel", function(e) {
                $(".transition-timer-carousel-progress-bar", this).addClass("animate").css("width", "100%")
            }), $(".transition-timer-carousel-progress-bar", ".transition-timer-carousel").css("width", "100%")
        }), $("#custom_carousel").on("slide.bs.carousel", function(e) {
            $("#custom_carousel .controls li.active").removeClass("active"), $("#custom_carousel .controls li:eq(" + $(e.relatedTarget).index() + ")").addClass("active")
        })
    })
}(jQuery);