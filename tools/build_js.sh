#!/bin/bash
MTV_PATH='./mtv'

cat $MTV_PATH/coffee/mtv.coffee \
    $MTV_PATH/coffee/mtv.store.coffee \
    | coffee --compile --stdio --print | cat \
    $MTV_PATH/devjs/jquery.cookie.js \
    - | uglifyjs -o $MTV_PATH/mtv.min.js
