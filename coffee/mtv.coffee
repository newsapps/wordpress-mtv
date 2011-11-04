unless $ then $ = jQuery

window.MTV = new Object
    debugging: () ->
        if WordPress.DEBUG and @hasConsole() then yes
        else no

    hasConsole: `function() {
        try {
            return 'console' in window && window['console'] !== null;
        } catch (e) {
            return false;
        }
    }`

    do_ajax: (url, data, success, error) ->
        json = JSON.stringify data

        params =
            url: WordPress.ajaxurl
            type: 'POST'
            data: {action: 'mtv', path: url, data: json}
            dataType: 'json'
            success: (data, textStatus, jqXHR) =>
                if @debugging()
                    console.log("do_ajax: #{textStatus}")
                    console.log(data)
                    console.log(jqXHR)
                if success then success(data, textStatus, jqXHR)
            error: (jqXHR, textStatus, errorThrown) =>
                if @debugging()
                    console.log("do_ajax: #{textStatus}")
                    console.log(jqXHR)
                    console.log(errorThrown)
                if error then error(jqXHR, textStatus, errorThrown)

        # Add a delay to ajax calls if running locally
        if @debugging()
            setTimeout(
                () => $.ajax(params),
                2000
            )
        else $.ajax params

#
# Custom Backbone sync function that works with wordpress ajax
#
if Backbone in window or window['Backbone'] isnt null
    Backbone.sync = (method, model, success, error) ->

        data = if method is 'create' or method is 'update' then model.toJSON() else null

        url = unless method is 'read' then "#{model.url()}/#{method}" else model.url()

        MTV.do_ajax url, data, success, error
