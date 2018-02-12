/**
 * Created by Joql on 2018/2/12.
 */
var canvas = document.querySelector('canvas');
var ctx = canvas.getContext('2d');

var width = canvas.width = window.innerWidth;
var height = canvas.height = window.innerHeight;

var balls = [];

/**
 * 取随机数
 * @param min
 * @param max
 * @returns {*}
 */
function random(min,max) {
    var num = Math.floor(Math.random() * (max-min+1))+min;
    return num;
}
/**
 * 弹跳球实例化
 * @param x
 * @param y
 * @param velx
 * @param vely
 * @param color
 * @param size
 * @constructor
 */
function Ball(x, y, velx, vely, color, size) {
    this.x = x;
    this.y = y;
    this.velx = velx;
    this.vely = vely;
    this.color = color;
    this.size = size;
}
/**
 * 绘制
 */
Ball.prototype.draw = function () {
    ctx.beginPath();
    ctx.fillStyle = this.color;
    ctx.arc(this.x, this.y, this.size, 0, 2*Math.PI);
    ctx.fill();
}
/**
 * 更新位置
 */
Ball.prototype.update = function () {
    if( (this.x + this.size)>=width || (this.x - this.size)<=0){
        this.velx = -(this.velx);
    }

    if( (this.y + this.size)>=height || (this.y - this.size)<=0 ){
        this.vely = -(this.vely);
    }

    this.x += this.velx;
    this.y += this.vely;
}
/**
 * 撞击变色
 */
Ball.prototype.collisionDetect = function () {
    for (var j=0;j<balls.length;j++){
        if(!(this === balls[j])){
            var dx =this.x - balls[j].x;
            var dy = this.y - balls[j].y;
            var distance = Math.sqrt(dx*dx+dy*dy);
            if(distance < this.size+balls[j].size){
                balls[j].color = this.color =  'rgb('+random(0,255)+','+random(0,255)+','+random(0,255);
            }
        }
    }
    
}

function loop() {
    ctx.fillStyle = 'rgba(0, 0, 0, 0.25)';
    ctx.fillRect(0, 0, width, height);

    while (balls.length<25){
        var  ball = new Ball(
            random(0,width),
            random(0,height),
            random(-7,7),
            random(-7,7),
            'rgb('+random(0,255)+','+random(0,255)+','+random(0,255),
            random(10,20)
        );
        balls.push(ball);
    }

    for(var i=0; i< balls.length;i++){
        balls[i].draw();
        balls[i].update();
        balls[i].collisionDetect();
    }

    //setTimeout('loop()',10);
    //动画神器
    requestAnimationFrame(loop)
}