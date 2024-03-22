const jwt = require('jsonwebtoken')
const JWTsecret = 'xxxxxx'

module.exports = {
    /**
     * 验证token
     * @params token
     */
    getToken:function(token){
        return new Promise((resolve, reject) =>
        {
          if(!token) 
          {
            reject({error: 'token是空的'})
          }
          else
          {
            console.log('token=',token)
            const info = jwt.verify(token.split(' ')[1], JWTsecret)
            console.log('info=',info)
            resolve(info) //解析返回的值
          }
        })
    }
}
