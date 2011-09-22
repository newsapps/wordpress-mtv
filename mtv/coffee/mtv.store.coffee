unless $ then $ = jQuery

#
# Simple storage API
# Uses HTML5 localStorage if available. Otherwise falls back to cookie storage.
# Always uses a cookie to expire storage. Defaults to the length of the session.
#
window.MTV.Store = new Object
    expires: null # session

    get: (key) ->
        if @has_local_storage()
            json_data = if $.cookie(key + '-timer') then localStorage.getItem(key) else null
        else
            json_data = $.cookie(key)

        # Deserialize
        if json_data then JSON.parse json_data else no

    save: (key, val) ->
        # Prep data
        json_data = JSON.stringify val

        if @has_local_storage()
            $.cookie(key + '-timer', 'true',
                expires: @expires
                path: '/'
            )
            localStorage.setItem(key, json_data)
        else
            $.cookie(key, json_data,
                expires: @expires
                path: '/'
            )

    remove: (key) ->
        if @has_local_storage()
            $.cookie(key + '-timer', null,
                path: '/'
            )
            localStorage.removeItem(key)
        else
            $.cookie(key, null,
                path: '/'
            )

    has_local_storage: `function() {
        try {
            return 'localStorage' in window && window['localStorage'] !== null;
        } catch (e) {
            return false;
        }
    }`

