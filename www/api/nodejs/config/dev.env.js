var merge = require('webpack-merge')
var prodEnv = require('./prod.env')

module.exports = merge(prodEnv, {
    NODE_ENV: '"development"',
    default_domain_api: "http://api.w.datacdn.cn",
    default_domain__local_api: 'http://localhost',
})
