var createError = require('http-errors');
var express = require('express');
const cors = require('cors');
var path = require('path');
var cookieParser = require('cookie-parser');
var logger = require('morgan');
const gobalConfig = require('./config/gobal');
const expressJWT = require('express-jwt');

require('dotenv').config();

var app = express();

// 页面模板引擎设置[弃用]
// app.set('views', path.join(__dirname, 'views'));
// app.set('view engine', 'jade');

// 允许跨域请求
app.all('*', function (req, res, next) {
    res.header("Access-Control-Allow-Origin", "*");
    res.header("Access-Control-Allow-Headers", "X-Requested-With");
    res.header("Access-Control-Allow-Methods", "PUT,POST,GET,DELETE,OPTIONS");
    res.header("X-Powered-By", ' 3.2.1')
    res.header("Content-Type", "application/json;charset=utf-8");
    next();
});

// socket.io
const { createServer } = require("http");
const { Server } = require("socket.io");
const httpServer = createServer(app);
const io = new Server(httpServer, { 
    cors: {
        origin: gobalConfig.originDomain,
        credentials: true,
      },
 });

// chat server statup
io.on('connection', function(socket) { 

    console.log('user socket_id:',socket.id)

    // 进入房间
    socket.on('joinRoom',function(data){
        const room_id = data.room_id.toString()
        const user_id = data.user_id.toString()
        socket.join(room_id);
        socket.to(room_id).emit("user-connected", user_id);
    }); 

    // 接收聊天信息
    socket.on('msg',function(data){ 
        const room_id = data.room_id.toString()
        const msg = data.msg
        socket.to(room_id).emit('chat',msg,room_id);//发送聊天信息给房间内所有用户
    });

    // 离开房间
    socket.on('leaveRoom',function(data){
        const room_id = data.room_id.toString()
        const user_id = data.user_id.toString()
        socket.to(room_id).emit("user-leaved", user_id);
        socket.leave(room_id);
    }); 

    // 断开连接
    socket.on('disconnect',function(){ 
        // io.sockets.emit('disconnect'); 
        console.log("断开",socket.id);
    }) 
}); 

httpServer.listen(1333,'127.0.0.1', function(){
    console.log(`Socket.IO server running :1333`);
});

app.use(logger('dev'));
app.use(express.json());
app.use(express.urlencoded({ extended: false }));
app.use(cookieParser());
app.use(express.static(path.join(__dirname, 'public')));

// 验证token
app.use(expressJWT({
    secret: gobalConfig.JWTsecret, // 签名的密钥
    algorithms: ['HS256']
}).unless({
    path: ['/socket.io',]  // 指定路径不经过 Token 解析
}))

// token验证错误处理
app.use(function (err, req, res, next) {
    console.log(err.name);
    if (err.name === 'UnauthorizedError') {   
      res.send('token错误')
    }
})

// catch 404 and forward to error handler
app.use(function(req, res, next) {
  next(createError(404));
});

// error handler
app.use(function(err, req, res, next) {
  // set locals, only providing error in development
  res.locals.message = err.message;
  res.locals.error = req.app.get('env') === 'development' ? err : {};

  // render the error page
  res.status(err.status || 500);
  res.render('error');
});


module.exports = app;
