#!/usr/bin/lua

package.path = package.path .. ";./?.lua;./app/?.lua"

local forum = require "forum"
forum.main()
