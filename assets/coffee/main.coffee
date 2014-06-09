#================================
# begin jquery anonymous wrapper
#================================

(($) ->
    'use strict'

    # common vars
    $window = $ window
    $document = $ document
    $body = null

    #=======================================================
    # smoothly scroll to an element
    #=======================================================

    smoothScrollTo = ( $el, duration, offset ) ->
        if $el.length > 0
            duration = if isNaN(duration) then 800 else duration
            offset = if isNaN(offset) then 0 else offset
            scroll_to = $el.offset().top + offset
            if $document.scrollTop() != scroll_to
                $('html,body').animate
                    scrollTop: scroll_to
                ,
                    queue: false
                    duration: duration
                    easing: 'swing'
        return

    #=======================================================
    # begin campfires (domready scripts separated per page)
    #=======================================================

    campfires =

        #=======================
        # campfire: "all pages"
        #=======================

        common:
            init: ->
                $body = $ 'body'

                #===================================
                # open external links in new window
                #===================================

                $body.on
                    click: ->
                        if location.hostname!=this.hostname
                            this.target = '_blank'
                        return
                , 'a[href*="//"]'

                #==================================
                # smooth scrolling to page anchors
                #==================================

                $body.on
                    click: (ev) ->
                        if location.pathname.replace(/^\//,'')==this.pathname.replace(/^\//,'') and location.hostname==this.hostname
                            $this = $(this)
                            duration = parseInt $(this).data('duration')
                            $target = $(this.hash)
                            $target = if $target.length then $target else $('[name=' + this.hash.slice(1) +']')
                            if $target.length
                                ev.preventDefault()
                                $(':focus').blur()
                                if duration>0
                                    smoothScrollTo $target, duration
                                    console.debug 1, duration
                                else
                                    smoothScrollTo $target
                                    console.debug 0, duration
                        return
                , 'a[href*=#]:not([href=#])'

                #====================
                # bootstrap tooltips
                #====================

                $('[data-toggle="tooltip"]').each ->
                    $this = $(this)
                    title = if $this.attr('title') then $this.attr('title') else false
                    if title
                        delay = if $this.data('delay') then $this.data('delay').toString().split('|') else ['100']
                        delay = if delay && delay.length==2 then delay else [delay[0],'50']
                        delay =
                            show: parseInt delay[0].trim()
                            hide: parseInt delay[1].trim()
                        $this.removeAttr('title')
                        $this.tooltip
                            html: true
                            delay: delay
                            title: title.replace /\/\//g, '<br>'
                    return

                #=======================================================
                # stick footer to bottom if page is smaller than window
                #=======================================================

                $('.site-footer').each ->
                    $footer = $ this
                    $window.on
                        'resize.stickyFooter': ->
                            if ( $body.outerHeight() + if $footer.hasClass('fixed') then $footer.outerHeight() else 0 ) <= $window.height()
                                $footer.addClass 'fixed'
                            else
                                $footer.removeClass 'fixed'
                            return
                        load: ->
                            $window.trigger 'resize.stickyFooter'
                            return
                    .trigger 'resize.stickyFooter'
                    return

                #=======================
                # checkbox replacements
                #=======================

                $('[type="checkbox"]').each ->
                    $checkbox = $(this)
                    $replacement = $('<a href="" class="checkbox-replacement"></a>')
                    $checkbox.on
                        click: (ev) ->
                            $replacement.trigger 'setState'
                    $replacement.on
                        click: (ev) ->
                            ev.preventDefault()
                            $checkbox.prop 'checked', !$checkbox.prop('checked')
                            $replacement.trigger 'setState'
                            return
                        setState: ->
                            if $checkbox.prop 'checked'
                                $replacement.addClass('checked')
                            else
                                $replacement.removeClass('checked')
                            return
                        keypress: (ev) ->
                            if ev.which==32 # space
                                ev.preventDefault()
                                ev.stopPropagation()
                                $replacement.trigger 'click'
                                return false
                    .insertBefore $checkbox.hide()
                    .trigger 'setState'
                    return

        #==================
        # campfire: "home"
        #==================

        home:
            init: ->

                #======================
                # contact form display
                #======================

                $('#kontaktformular').each ->
                    $this = $(this).hide()
                    $this.on
                        toggleDisplay: ->
                            $this.slideToggle()
                            return
                    $('a[href="#'+this.id+'"]').on
                        click: (ev) ->
                            $(this).find('.fa').toggleClass('fa-eye fa-eye-slash')
                            $this.trigger('toggleDisplay')
                            return
                    return

                #======================
                # contact form handler
                #======================

                $('.form-contact').each ->
                    $form = $(this)
                    requestRunning = false
                    $form.on
                        sendData: (ev, testObj) ->
                            $testObj = $(testObj)
                            if !requestRunning
                                $.ajax
                                    type: 'POST'
                                    url: $form.attr('action')
                                    dataType: 'json'
                                    data: $form.serialize() + '&cfd=1' + if $testObj.length then '&cfd_test='+$testObj.attr('name') else ''
                                    beforeSend: ->
                                        if !$testObj.length
                                            $form.addClass('loading')
                                            requestRunning = true
                                        return
                                    success: (data) ->
                                        if !data.test
                                            $form.find('.form-group.has-error').removeClass('has-error').end().find('.response,.alert').remove()
                                        else
                                            $form.find('[name="'+data.test+'"]').closest('.form-group').removeClass('has-error').find('.response').remove()
                                            $form.find('#cfd_submit').closest('.form-group').removeClass('has-error').find('.response').remove()
                                        if !data.success || data.test
                                            if data.errors.length && data.errors[0][0]=='#'
                                                $form.append '<div class="alert alert-danger"><span class="glyphicon glyphicon-remove pull-right"></span>'+data.errors[0][1]+'</div>'
                                            else
                                                for error in data.errors
                                                    $group = $form.find('#cfd_'+error[0]).closest('.form-group').addClass('has-error')
                                                    $group.append '<p class="response help-block">'+error[1]+'</p>'
                                                if !data.test
                                                    $form.find('#cfd_submit').closest('.form-group').addClass('has-error').append('<p class="response help-block">Bitte pr&uuml;fe deine Eingaben.</p>')
                                        else
                                            $form.append '<div class="alert alert-success"><span class="glyphicon glyphicon-ok pull-right"></span>'+data.success+'</div>'
                                            $form.find(':input').not(':checkbox').val('')
                                        return
                                    error: (XMLHttpRequest, textStatus, errorThrown) ->
                                        $form.append '<div class="alert alert-danger"><span class="glyphicon glyphicon-remove pull-right"></span><strong>'+textStatus+'</strong>'+errorThrown+'</div>'
                                    complete: ->
                                        if !$testObj.length
                                            $form.removeClass('loading')
                                            requestRunning = false
                                        $form.find('#cfd_submit').blur()
                                        return
                            return
                        submit: (ev) ->
                            ev.preventDefault()
                            $form.trigger 'sendData'
                            return
                    .find('#cfd_name,#cfd_email,#cfd_msg').on
                        change: ->
                            $form.trigger 'sendData', this
                            return

                #=================
                # portfolio index
                #=================

                $('.portfolio-index').each ->

                    #-----------------------------------------
                    # portfolio properties
                    #-----------------------------------------

                    img_size = 600 # size of background layer image
                    max_viewport_size = 440 # maximum size of viewport in active mode ( = visible area )
                    duration_reset = 800 # duration for resetting background layer position in mouse-leave
                    duration_move = 400 # duration for moving background layer position relative to mouse-move
                    duration_hide_item = 1600
                    duration_show_item = 600
                    duration_hide_caption = 400
                    duration_show_caption = 1200
                    duration_show_details = 800
                    duration_hide_details = 800
                    delay_slideup_details = 400
                    delay_caption = 800
                    opacity_hide_item = 0.0005
                    offset_scrollto_details = 20

                    $index = $(this)
                    $items = $index.find('>.item')
                    $details_container = $('.portfolio-details-container')
                    $active_item = null
                    request_details_running = false

                    threshold = ( img_size - max_viewport_size ) / 2; # movable threshold depending on image size
                    half_max_viewport_size = ( max_viewport_size / 2 ) # half viewport size for calculations relative to viewport center

                    #-------------------------------------
                    # properties depending on window size
                    #-------------------------------------

                    window_width = 0
                    window_height = 0

                    $window.on
                        'resize.portfolioIndex': (ev) ->
                            window_width = $window.width()
                            window_height = $window.height()
                            return

                    #-----------------------------------------
                    # move background layer by mouse position
                    #-----------------------------------------

                    moveArea = ( $area, x, y, duration ) ->
                        x = if isNaN(x) then 0 else x
                        y = if isNaN(y) then 0 else y
                        duration = if isNaN(duration) then duration_reset else duration
                        $area.animate
                            left: -x
                            top:  -y
                        ,
                            queue: false
                            duration: duration
                            easing: 'easeOutCubic'
                        return

                    #-------------------------
                    # set an item as "active"
                    #-------------------------

                    setActive = ( $item ) ->
                        if $active_item != $item
                            if $active_item and $active_item.length
                                $active_item.removeClass('active').trigger('hideCaption')
                            $active_item = $item
                            $item.addClass('active')
                            window.setTimeout ->
                                $item.trigger('showCaption')
                            , delay_caption
                            changeActive()
                        return

                    #---------------------------
                    # set an item as "inactive"
                    #---------------------------

                    unsetActive = ( $item ) ->
                        if $active_item == $item
                            $active_item = null
                            $item.removeClass('active').trigger('hideCaption')
                            changeActive()
                        return

                    #----------------------------
                    # change current active item
                    #----------------------------

                    changeActive = ->
                        if !$active_item
                            $items.trigger('showItem')
                        else
                            $active_item.trigger('showItem')
                            $items.not($active_item).trigger('hideItem')
                        return

                    #----------------------------
                    # load item details
                    #----------------------------

                    loadDetails = ( url, $item ) ->
                        if $details_container.data('current_url') == url
                            smoothScrollTo $details_container, undefined, offset_scrollto_details
                            $details_container.find('.screens').trigger 'startSlider'
                            return
                        if request_details_running
                            return
                        $.ajax
                            type: 'POST'
                            url: url
                            dataType: 'json'
                            data: 'ajax=1'
                            beforeSend: ->
                                $details_container.addClass 'loading'
                                if $item
                                    $item.addClass 'loading'
                                request_details_running = true
                                return
                            success: ( data ) ->
                                $.each data.images, (i) ->
                                    image = new Image()
                                    image.src = this.src
                                    if i==0
                                        image.onload = ->
                                            showDetails data
                                            return
                                        image.onerror = image.onload
                                        if image.complete
                                            image.onload()
                                return
                            error: ( XMLHttpRequest, textStatus, errorThrown ) ->
                                # console.debug textStatus, errorThrown
                                return
                            complete: ->
                                $details_container.removeClass 'loading'
                                if $item
                                    $item.removeClass 'loading'
                                request_details_running = false
                                return
                        return

                    #-------------------
                    # show item details
                    #-------------------

                    showDetails = ( data ) ->
                        $remaining_details = $details_container.find('.portfolio-details')
                        $details = $(data.html)
                        $details.css
                            opacity: 0
                        .appendTo($details_container).animate
                            opacity: 1
                        ,
                            duration: duration_show_details
                            easing: 'easeOutQuad'
                            queue: false
                            complete: ->
                                $details.find('.slider').trigger 'startSlider'
                                return
                        $details.find('.slider').trigger 'initSlider'
                        hideDetails $remaining_details.addClass('prev')
                        if $remaining_details.length==0
                            $details_container.hide().slideDown
                                duration: duration_show_details
                                easing: 'easeOutQuad'
                        smoothScrollTo $details_container, undefined, offset_scrollto_details
                        return

                    #-------------------
                    # hide item details
                    #-------------------

                    hideDetails = ( $details ) ->
                        if $details.length>0
                            $details.find('.slider').trigger 'stopSlider'
                            $details.animate
                                opacity: 0
                            ,
                                duration: duration_hide_details
                                easing: 'easeInQuad'
                                queue: false
                                complete: ->
                                    $details.remove()
                                    return
                            $remaining_details = $details_container.find('.portfolio-details').not($details[0])
                            if $remaining_details.length==0
                                $details_container.css
                                    height: $details_container.outerHeight()
                                .delay(delay_slideup_details).slideUp
                                    duration: duration_hide_details
                                    easing: 'easeInQuad'
                        return

                    #--------------------
                    # walk through items
                    #--------------------

                    $items.each ->

                        #-------------------------------
                        # child elements and properties
                        #-------------------------------

                        $item = $(this)
                        $figure = $item.find('figure')
                        $caption = $figure.find('figcaption')
                        $viewport = $figure.find('.viewport')
                        $area = $viewport.find('.moving-area')
                        $link = $caption.find('a.btn')

                        figure_width = $figure.width()
                        figure_height = $figure.height()

                        $caption.hide()

                        #----------------------------
                        # hide item (inactive state)
                        #----------------------------

                        hideItem = ->
                            $item.stop().animate
                                opacity: opacity_hide_item
                            ,
                                queue: false
                                duration: duration_hide_item
                                easing: 'easeInOutCirc'
                                progress: ->
                                    if !$active_item or $active_item == $item
                                        showItem()
                                    return
                            return

                        #-------------------------------------
                        # show item (default or active state)
                        #-------------------------------------

                        showItem = ->
                            easing = if $active_item==$item then 'easeOutQuart' else 'easeInOutCirc'
                            $item.stop().animate
                                opacity: 1
                            ,
                                queue: false
                                duration: duration_show_item
                                easing: easing
                                progress: ->
                                    if $active_item and $active_item != $item
                                        hideItem()
                                    return
                            return

                        #-------------------------------------------------------------
                        # show caption, calculate position depending on item position
                        #-------------------------------------------------------------

                        showCaption = ->
                            if $active_item == $item
                                item_width = $item.width()
                                item_height = $item.height()
                                item_offset = $item.offset()
                                caption_width = $caption.outerWidth()
                                caption_height = $caption.outerHeight()
                                # item center offset relative to window
                                item_center_left = item_offset.left + ( item_width / 2 )
                                item_center_top = ( item_offset.top - $document.scrollTop() ) + ( item_height / 2 )
                                # horizontal item position from 0 to 1
                                item_hpos = item_center_left / window_width
                                # vertical item position from 0 to 1
                                item_vpos = item_center_top / window_height
                                # on screens with only one row (item centered), caption centered above/below
                                if window_width < 768 and 0.4 < item_hpos < 0.6
                                    x = figure_width / 2 - caption_width / 2
                                    caption_class = 'center'
                                    # if item in upper window area, caption below
                                    if item_vpos < 0.5
                                        y = figure_height + ( half_max_viewport_size - figure_height / 2 ) - 70
                                    # if item in lower window area, caption above
                                    else
                                        y = -( half_max_viewport_size - figure_height / 2 ) - caption_height + 70
                                # if item not centered
                                else
                                    # if item left, caption right
                                    if item_hpos < 0.5
                                        x = figure_width / 2 + half_max_viewport_size
                                        caption_class = 'left'
                                    # if item right, caption left
                                    else
                                        x = - ( half_max_viewport_size - figure_height / 2 ) - caption_width
                                        caption_class = 'right'
                                    # vertical caption position at center of viewport
                                    y = figure_height / 2 - caption_height / 2
                                    # move caption up or down by a third of viewport, depending on vertical item position in window
                                    y += max_viewport_size * 0.2 * ( if item_vpos < 0.33 then 1 else -1 )
                                $caption.show().attr( 'class', caption_class ).css
                                    top: y
                                    left: x
                                    opacity: 0
                                .animate
                                    opacity: 1
                                ,
                                    duration: duration_show_caption
                                    queue: false
                                    easing: 'easeOutCirc'
                            return

                        #--------------
                        # hide caption
                        #--------------

                        hideCaption = ->
                            $caption.animate
                                opacity: 0
                            ,
                                duration: duration_hide_caption
                                queue: false
                                easing: 'easeOutCirc'
                                complete: ->
                                    $(this).hide()
                            return

                        #------------------------------
                        # map item functions on events
                        #------------------------------

                        $item.on
                            hideCaption: ->
                                hideCaption()
                            showCaption: ->
                                showCaption()
                            hideItem: ->
                                hideItem()
                            showItem: ->
                                showItem()

                        #-------------------------------------------------------------------------
                        # hoverIntent - http://cherne.net/brian/resources/jquery.hoverIntent.html
                        #-------------------------------------------------------------------------

                        $item.hoverIntent
                            over: (ev) ->
                                setActive $item
                                return
                            out: (ev) ->
                                unsetActive $item
                                moveArea $area
                                return
                            timeout: 50
                            sensitivity: 30
                            interval: 50

                        #-----------------------------------------------
                        # events on viewport layer / actual user events
                        #-----------------------------------------------

                        $viewport.on
                            click: (ev) ->
                                ev.stopPropagation()
                                # if touch device, click toggles active state
                                if Modernizr.touch
                                    if $active_item == $item
                                        unsetActive $item
                                        moveArea $area
                                    else
                                        setActive $item
                                # if no-touch device
                                else
                                    $link.trigger('click')
                                return
                            mousemove: (ev) ->
                                if $active_item == $item
                                    viewport_offset = $viewport.offset()
                                    viewport_offset_diff = ( max_viewport_size - $viewport.width() ) / 2
                                    viewport_offset_x = viewport_offset.left - viewport_offset_diff
                                    viewport_offset_y = viewport_offset.top - viewport_offset_diff
                                    # cursor offset relative to viewport
                                    cursor_offset_x = ( ( ev.pageX - viewport_offset_x ) - half_max_viewport_size ) / half_max_viewport_size
                                    cursor_offset_y = ( ( ev.pageY - viewport_offset_y ) - half_max_viewport_size ) / half_max_viewport_size
                                    # move background layer within threshold
                                    move_x = threshold * cursor_offset_x
                                    move_y = threshold * cursor_offset_y
                                    moveArea $area, move_x, move_y, duration_move
                                return

                        #-------------------
                        # link: "show details"
                        #-------------------

                        $link.on
                            click: (ev) ->
                                ev.stopPropagation()
                                ev.preventDefault()
                                loadDetails $link.prop('href'), $item
                                return

                        #-------------------
                        # end of items loop
                        #-------------------

                        return

                    #-----------------
                    # details - close
                    #-----------------

                    $document.on
                        click: (ev) ->
                            ev.stopPropagation()
                            ev.preventDefault()
                            $this = $(this)
                            $this.fadeOut
                                duration: delay_slideup_details
                                easing: 'swing'
                            hideDetails $this.closest('.portfolio-details')
                            return
                    , '.portfolio-details .close'

                    # initially calculate properties depending on window size
                    $window.trigger 'resize.portfolioIndex'

                    #-----------------------------
                    # end of portfolio index loop
                    #-----------------------------

                    return

                #========
                # Slider
                #========

                duration_slider = 2000
                duration_slider_delay = 2000

                $document.on

                    #-------------------
                    # initialize slider
                    #-------------------

                    initSlider: ( ev, slide_no ) ->
                        if !this.$slides
                            slide_no = if isNaN(slide_no) then 0 else slide_no
                            $this = $ this
                            this.$slides = $this.find '.slider-item'
                            this.$slides.stop()
                            this.slide_active_no = slide_no
                            this.slide_prev_no = if slide_no-1 < 0 then this.$slides.length - 1 else slide_no-1
                            $(this.$slides[this.slide_active_no]).addClass 'active'
                            $(this.$slides[this.slide_prev_no]).addClass 'prev'
                            this.$slides.not(this.$slides[this.slide_active_no]).css
                                opacity: 0
                            $this.trigger 'initIndicators'
                            $this.trigger 'initNav'

                    #--------------
                    # start slider
                    #--------------

                    startSlider: ( ev, slide_no ) ->
                        $this = $ this
                        $this.trigger 'initSlider', [slide_no]
                        $this.trigger 'stopSlider'
                        $this.trigger 'setSlideTimeout'
                        return

                    #-------------
                    # stop slider
                    #-------------

                    stopSlider: (ev) ->
                        if this.$slides
                            this.$slides.stop()
                            clearTimeout this.slide_timeout, [ duration_slider_delay + duration_slider ]
                        return

                    #----------------------
                    # start slider timeout
                    #----------------------

                    setSlideTimeout: (ev, delay ) ->
                        $this = $(this)
                        delay = if isNaN(delay) then duration_slider_delay else delay
                        clearTimeout this.slide_timeout
                        this.slide_timeout = window.setTimeout ->
                            $this.trigger 'nextSlide'
                            return
                        , delay
                        return

                    #-------------
                    # go to slide
                    #-------------

                    gotoSlide: ( ev, slide_no ) ->
                        $this = $ this
                        if isNaN(slide_no)
                            return
                        $this.trigger 'initSlider', [slide_no]
                        # if this.$slides.filter(':animated').length > 0
                        #     return
                        clearTimeout this.slide_timeout
                        slider_ani_options =
                            queue: false
                            duration: duration_slider
                            easing: 'easeInOutQuad'
                            complete: ->
                                $this.trigger 'setSlideTimeout'
                                return
                        $(this.$slides[slide_no]).addClass('active').animate
                            opacity: 1
                        , slider_ani_options
                        if slide_no != this.slide_active_no
                            $(this.$slides[this.slide_active_no]).toggleClass('active prev').animate
                                opacity: 0
                            , slider_ani_options
                            $(this.$slides[this.slide_prev_no]).toggleClass('prev')
                            this.slide_prev_no = this.slide_active_no
                            this.slide_active_no = slide_no
                            this.$indicators.find('.active').removeClass('active').end().find('span:eq('+slide_no+')').addClass('active')
                        return

                    #------------------
                    # go to next slide
                    #------------------

                    nextSlide: (ev) ->
                        $this = $ this
                        $this.trigger 'initSlider'
                        next_slide = if this.slide_active_no+1 < this.$slides.length then this.slide_active_no+1 else 0
                        $this.trigger 'gotoSlide', [ next_slide ]
                        return

                    #----------------------
                    # go to previous slide
                    #----------------------

                    prevSlide: (ev) ->
                        $this = $ this
                        $this.trigger 'initSlider'
                        prev_slide = if this.slide_active_no-1 >= 0 then this.slide_active_no-1 else this.$slides.length-1
                        $this.trigger 'gotoSlide', [ prev_slide ]
                        return

                    #-----------------------
                    # initialize indicators
                    #-----------------------

                    initIndicators: (ev) ->
                        $this = $ this
                        this.$indicators = $ '<div class="slider-indicators"/>'
                        $.each this.$slides, (i) ->
                            $indicator = $('<span/>').on
                                click: (ev) ->
                                    $this.trigger 'gotoSlide', [i]
                                    return
                            if $this[0].slide_active_no == i
                                $indicator.addClass 'active'
                            $this[0].$indicators.append $indicator
                            return
                        if this.$slides.length > 1
                            $this.append this.$indicators
                        return

                    #--------------------------
                    # initialize prev/next nav
                    #--------------------------

                    initNav: (ev) ->
                        if this.$slides.length > 1
                            $this = $ this
                            $prev = $('<div class="slider-nav-prev"/>').on
                                click: (ev) ->
                                    ev.preventDefault()
                                    ev.stopPropagation()
                                    $this.trigger 'prevSlide'
                                    return
                            $next = $('<div class="slider-nav-next"/>').on
                                click: (ev) ->
                                    ev.preventDefault()
                                    ev.stopPropagation()
                                    $this.trigger 'nextSlide'
                                    return
                            $('<div class="slider-nav"/>').append($prev,$next).appendTo($this)
                        return

                #------------------------------
                # slider events trigger filter
                #------------------------------

                , '.slider'

                #=========================
                # end campfires.home.init
                #=========================

                return

        #===============
        # end campfires
        #===============

    #=============================
    # "lighter fires campfires...
    #=============================
    lighter =
        fire: (place) ->
            if campfires[place] and typeof campfires[place].init=='function'
                campfires[place].init()
        spread: ->
            lighter.fire 'common'
            for place in document.body.className.replace(/-/g,'_').split(/\s+/)
                lighter.fire place
            return

    #================
    # ...on domready
    #================
    $document.on
        ready: lighter.spread

    #==============================
    # end jquery anonymous wrapper
    #==============================
    return
)(jQuery)
