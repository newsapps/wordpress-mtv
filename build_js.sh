#!/bin/bash
MTV_PATH='.'

cat $MTV_PATH/coffee/mtv.coffee \
    | coffee --compile --stdio --print | cat \
    - | uglifyjs -o $MTV_PATH/mtv.min.js
