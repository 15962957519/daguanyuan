#!/bin/sh

#方便清空redis缓存

redis-cli -h 101.37.175.6 -p 6379 -a qwjktianok BGREWRITEAOF    2>&1
redis-cli -h 116.62.47.14 -p 6379 -a qwjktianok  BGREWRITEAOF   2>&1

