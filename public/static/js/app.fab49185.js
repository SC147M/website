(function(){"use strict";var t={5702:function(t,e,s){var i=s(9242),n=s(3396);function o(t,e,s,i,o,a){const h=(0,n.up)("HomeView"),r=(0,n.up)("MatchView");return(0,n.wg)(),(0,n.iD)("div",null,[1===o.appState?((0,n.wg)(),(0,n.j4)(h,{key:0,onStartMatch:a.startMatch},null,8,["onStartMatch"])):((0,n.wg)(),(0,n.j4)(r,{key:1,ref:"matchView",onOnSettingsClick:a.showSettings,"match-duration":o.matchDuration,"first-half":o.firstHalf,"second-half":o.secondHalf},null,8,["onOnSettingsClick","match-duration","first-half","second-half"]))])}var a=s(7139);const h={key:0,class:"layout"},r={class:"menu"},d={class:"canvas"},l={class:"match-clock"};function u(t,e,s,i,o,u){const c=(0,n.up)("canvas-display");return o.mounted?((0,n.wg)(),(0,n.iD)("div",h,[(0,n._)("div",r,[(0,n._)("button",{onClick:e[0]||(e[0]=t=>u.onSettingsClick()),class:"sbtn"},"Abort / Settings")]),(0,n._)("div",d,[(0,n.Wm)(c,{"seconds-for-shot":o.timeToShoot,msSecondsToShoot:o.mstimeToShoot,"shoot-time":u.shootTime},null,8,["seconds-for-shot","msSecondsToShoot","shoot-time"])]),(0,n._)("div",l,(0,a.zw)(o.matchTimeRemaining),1),(0,n._)("div",null,[(0,n._)("button",{onClick:e[1]||(e[1]=(...t)=>u.onButtonClick&&u.onButtonClick(...t)),class:"sbtn"},"Start / Stop")])])):(0,n.kq)("",!0)}const c={id:"myCanvas",width:"100",height:"100"};function m(t,e,s,i,o,a){return(0,n.wg)(),(0,n.iD)("canvas",c)}var f={name:"CanvasDisplay",props:{secondsForShot:Number,msSecondsToShoot:Number,shootTime:Number},watch:{secondsForShot(){this.update()}},data(){return{ctx:null,cW:0,cH:0,size:0}},methods:{init(){const t=document.getElementById("myCanvas");this.ctx=t.getContext("2d"),this.ctx.canvas.width=t.parentNode.offsetWidth,this.ctx.canvas.height=t.parentNode.offsetHeight,t.width=t.parentNode.offsetWidth,t.height=t.parentNode.offsetHeight,t.style.width=t.parentNode.offsetWidth+"px",t.style.height=t.parentNode.offsetHeight+"px",this.cW=t.parentNode.offsetWidth,this.cH=t.parentNode.offsetHeight,this.size=this.cW<this.cH?this.cW:this.cH,this.update()},update(){this.ctx.clearRect(0,0,this.ctx.canvas.width,this.ctx.canvas.height),this.drawShotTimeLeft();const t=.005,e=this.shootTime;this.drawCircle(e,Math.ceil(this.secondsForShot),t,this.size/2-60,"#ffffff",30),this.drawCircle(e,e,t,this.size/2-40,"#f5a700",2),this.drawShotMs()},drawShotTimeLeft(){this.ctx.font=this.size/2-55+"px Audiowide",this.ctx.textAlign="center",this.ctx.textBaseline="middle",this.ctx.fillStyle="rgba(255,255,255,1)",this.ctx.fillText(Math.ceil(this.secondsForShot).toString(),this.cW/2,this.cH/2)},drawShotMs(){this.ctx.beginPath(),this.ctx.lineWidth=10,this.ctx.strokeStyle="#5bab00";const t=0,e=this.msSecondsToShoot/100*(.3*this.cW),s=this.cH/2+(this.size/2-40)/2,i=this.cW/2-(e-t)/2;this.ctx.moveTo(t+i,s),this.ctx.lineTo(e+i,s),this.ctx.stroke()},drawCircle(t,e,s,i,n,o){this.ctx.strokeStyle=n,this.ctx.lineWidth=o;let a,h=0;const r=1/t*2*Math.PI;for(let d=0;d<e;d++)a=d*r-.5*Math.PI,h=(d+1)*r-.5*Math.PI,this.ctx.beginPath(),this.ctx.arc(this.cW/2,this.cH/2,i,a+s,h-s,!1),this.ctx.stroke()}},mounted(){this.init()}},p=s(89);const v=(0,p.Z)(f,[["render",m]]);var y=v,w={name:"MatchView",emits:["onSettingsClick"],components:{CanvasDisplay:y},props:{matchDuration:Number,firstHalf:Number,secondHalf:Number},computed:{shootTime(){return this.switchedTimer?this.secondHalf:this.firstHalf}},data(){return{matchStarted:!1,switchedTimer:!1,now:new Date,started:!1,startTime:null,matchTime:null,matchTimeRemaining:null,timeToShoot:0,mstimeToShoot:100,timerRunning:!1,mounted:!1,shotInterval:null,matchIntervall:null,timer5played:!1,timer4played:!1,timer3played:!1,timer2played:!1,timer1played:!1,secondHalfPlayed:!1,twoMinutesPlayed:!1,oneMinutePlayed:!1,tensecondsPlayed:!1,matchOverPlayed:!1,sounds:{timer5:null,timer4:null,timer3:null,timer2:null,timer1:null,foul:null,beep:null,secondHalf:null,twoMinutes:null,oneMinute:null,tenseconds:null,matchOver:null}}},methods:{onSettingsClick(){this.$emit("onSettingsClick")},start(){this.timeToShoot=this.shootTime,this.matchTime=new Date,this.matchTime.setMinutes(this.matchTime.getMinutes()+this.matchDuration),this.startTime=new Date,this.resetTimerSounds(),this.shotInterval=setInterval(this.handleTimer,10),this.matchIntervall=setInterval(this.handleMatchTimer,10),this.matchStarted=!0,this.switchedTimer=!1},handleMatchTimer(){this.now=new Date;const t=this.matchTime.getTime()-this.now.getTime();if(t/1e3<=120&&this.matchDuration>2&&(this.twoMinutesPlayed||(this.sounds.twoMinutes.play(),this.twoMinutesPlayed=!0)),t/1e3<=60&&this.matchDuration>1&&(this.oneMinutePlayed||(this.sounds.oneMinute.play(),this.oneMinutePlayed=!0)),t/1e3<=10&&(this.tensecondsPlayed||(this.sounds.tenseconds.play(),this.tensecondsPlayed=!0)),t<=0)return this.matchTimeRemaining="00:00",this.matchStarted=!1,this.timerRunning=!1,void(this.matchOverPlayed||(this.sounds.matchOver.play(),this.matchOverPlayed=!0));const e=Math.floor(t/1e3/60);let s=Math.floor(t/1e3%60);s<10&&(s="0"+s),this.matchTimeRemaining=e+":"+s;const i=this.now.getTime(),n=this.matchTime.getTime()-this.matchDuration/2*6e4,o=n-i;o<0&&!this.sounds.secondHalfPlayed&&(this.sounds.secondHalf.play(),this.sounds.secondHalfPlayed=!0),o<0&&!this.timerRunning&&!this.switchedTimer&&(this.switchedTimer=!0)},resetTimerSounds(){this.timer1played=!1,this.timer2played=!1,this.timer3played=!1,this.timer4played=!1,this.timer5played=!1},handleTimer(){if(!this.timerRunning)return;const t=new Date;this.timeToShoot=Math.round(1e3*this.shootTime+this.startTime.getTime()-t.getTime())/1e3,this.timeToShoot=Math.round(100*this.timeToShoot)/100,this.mstimeToShoot=Math.round((1e3*this.shootTime+this.startTime.getTime()-t.getTime())/10)%100,this.timeToShoot<5&&!this.timer5played&&(this.sounds.timer5.play(),this.timer5played=!0),this.timeToShoot<4&&!this.timer4played&&(this.sounds.timer4.play(),this.timer4played=!0),this.timeToShoot<3&&!this.timer3played&&(this.sounds.timer3.play(),this.timer3played=!0),this.timeToShoot<2&&!this.timer2played&&(this.sounds.timer2.play(),this.timer2played=!0),this.timeToShoot<1&&!this.timer1played&&(this.sounds.timer1.play(),this.timer1played=!0),this.timeToShoot<0&&(this.timeToShoot=0,this.timerRunning=!1,this.sounds.foul.play())},onButtonClick(){this.matchStarted?this.resetTimerSounds():this.start(),this.timerRunning?(this.sounds.beep.load(),this.timerRunning=!1):(this.sounds.beep.play(),this.startTime=new Date,this.timeToShoot=this.shootTime,this.timerRunning=!0)},reset(){this.resetTimerSounds(),this.secondHalfPlayed=!1,this.twoMinutesPlayed=!1,this.oneMinutePlayed=!1,this.tensecondsPlayed=!1,this.matchOverPlayed=!1,this.matchStarted=!1,this.timerRunning=!1,this.timeToShoot=this.firstHalf,this.mstimeToShoot=100,this.matchTimeRemaining=this.matchDuration+":00",this.mounted=!0,this.switchedTimer=!1,clearInterval(this.shotInterval),clearInterval(this.matchIntervall)}},beforeUnmount(){this.reset()},mounted(){this.sounds.timer5=new Audio("/assets/sounds/5.mp3"),this.sounds.timer5.load(),this.sounds.timer4=new Audio("/assets/sounds/4.mp3"),this.sounds.timer4.load(),this.sounds.timer3=new Audio("/assets/sounds/3.mp3"),this.sounds.timer3.load(),this.sounds.timer2=new Audio("/assets/sounds/2.mp3"),this.sounds.timer2.load(),this.sounds.timer1=new Audio("/assets/sounds/1.mp3"),this.sounds.timer1.load(),this.sounds.foul=new Audio("/assets/sounds/foul.mp3"),this.sounds.foul.load(),this.sounds.beep=new Audio("/assets/sounds/beep.mp3"),this.sounds.beep.load(),this.sounds.secondHalf=new Audio("/assets/sounds/secondhalfshottimer.mp3"),this.sounds.secondHalf.load(),this.sounds.twoMinutes=new Audio("/assets/sounds/2minutesleft.mp3"),this.sounds.twoMinutes.load(),this.sounds.oneMinute=new Audio("/assets/sounds/1minuteleft.mp3"),this.sounds.oneMinute.load(),this.sounds.tenseconds=new Audio("/assets/sounds/10seconds.mp3"),this.sounds.tenseconds.load(),this.sounds.matchOver=new Audio("/assets/sounds/matchover.mp3"),this.sounds.matchOver.load(),this.sounds.matchOver.addEventListener("canplaythrough",this.reset(),!1)}};const S=(0,p.Z)(w,[["render",u],["__scopeId","data-v-e6b28942"]]);var T=S;const b=t=>((0,n.dD)("data-v-4f3b5636"),t=t(),(0,n.Cn)(),t),g={style:{"max-width":"100vw"}},M=b((()=>(0,n._)("span",null,"Match Duration",-1))),H=(0,n.uE)('<option value="1" data-v-4f3b5636>1 Minutes</option><option value="3" data-v-4f3b5636>3 Minutes</option><option value="10" data-v-4f3b5636>10 Minutes</option><option value="15" data-v-4f3b5636>15 Minutes</option><option value="20" data-v-4f3b5636>20 Minutes</option><option value="25" data-v-4f3b5636>25 Minutes</option><option value="30" data-v-4f3b5636>30 Minutes</option>',7),x=[H],k=b((()=>(0,n._)("span",null,"First Half",-1))),D=(0,n.uE)('<option value="5" data-v-4f3b5636>5 Seconds</option><option value="10" data-v-4f3b5636>10 Seconds</option><option value="15" data-v-4f3b5636>15 Seconds</option><option value="20" data-v-4f3b5636>20 Seconds</option><option value="25" data-v-4f3b5636>25 Seconds</option>',5),P=[D],C=b((()=>(0,n._)("span",null,"Second Half",-1))),_=(0,n.uE)('<option value="5" data-v-4f3b5636>5 Seconds</option><option value="10" data-v-4f3b5636>10 Seconds</option><option value="15" data-v-4f3b5636>15 Seconds</option><option value="20" data-v-4f3b5636>20 Seconds</option><option value="25" data-v-4f3b5636>25 Seconds</option>',5),O=[_];function I(t,e,s,o,a,h){return(0,n.wg)(),(0,n.iD)("div",g,[(0,n._)("div",null,[(0,n._)("label",null,[M,(0,n.wy)((0,n._)("select",{"onUpdate:modelValue":e[0]||(e[0]=t=>a.matchDuration=t)},x,512),[[i.bM,a.matchDuration]])])]),(0,n._)("div",null,[(0,n._)("label",null,[k,(0,n.wy)((0,n._)("select",{"onUpdate:modelValue":e[1]||(e[1]=t=>a.firstHalf=t)},P,512),[[i.bM,a.firstHalf]])])]),(0,n._)("div",null,[(0,n._)("label",null,[C,(0,n.wy)((0,n._)("select",{"onUpdate:modelValue":e[2]||(e[2]=t=>a.secondHalf=t)},O,512),[[i.bM,a.secondHalf]])])]),(0,n._)("div",null,[(0,n._)("button",{onClick:e[3]||(e[3]=(...t)=>h.onBtnClick&&h.onBtnClick(...t)),class:"btn"},"Start match")])])}var A={name:"HomeView",emits:["startMatch"],data(){return{matchDuration:10,firstHalf:15,secondHalf:10}},methods:{onBtnClick(){this.$emit("startMatch",this.matchDuration,this.firstHalf,this.secondHalf)}}};const R=(0,p.Z)(A,[["render",I],["__scopeId","data-v-4f3b5636"]]);var W=R,N={name:"App",components:{HomeView:W,MatchView:T},data(){return{appState:1,matchDuration:10,firstHalf:15,secondHalf:10}},methods:{startMatch(t,e,s){this.matchDuration=parseInt(t),this.firstHalf=parseInt(e),this.secondHalf=parseInt(s),this.appState=2},showSettings(){this.appState=1}}};const V=(0,p.Z)(N,[["render",o]]);var j=V;(0,i.ri)(j).mount("#app")}},e={};function s(i){var n=e[i];if(void 0!==n)return n.exports;var o=e[i]={exports:{}};return t[i](o,o.exports,s),o.exports}s.m=t,function(){var t=[];s.O=function(e,i,n,o){if(!i){var a=1/0;for(l=0;l<t.length;l++){i=t[l][0],n=t[l][1],o=t[l][2];for(var h=!0,r=0;r<i.length;r++)(!1&o||a>=o)&&Object.keys(s.O).every((function(t){return s.O[t](i[r])}))?i.splice(r--,1):(h=!1,o<a&&(a=o));if(h){t.splice(l--,1);var d=n();void 0!==d&&(e=d)}}return e}o=o||0;for(var l=t.length;l>0&&t[l-1][2]>o;l--)t[l]=t[l-1];t[l]=[i,n,o]}}(),function(){s.n=function(t){var e=t&&t.__esModule?function(){return t["default"]}:function(){return t};return s.d(e,{a:e}),e}}(),function(){s.d=function(t,e){for(var i in e)s.o(e,i)&&!s.o(t,i)&&Object.defineProperty(t,i,{enumerable:!0,get:e[i]})}}(),function(){s.g=function(){if("object"===typeof globalThis)return globalThis;try{return this||new Function("return this")()}catch(t){if("object"===typeof window)return window}}()}(),function(){s.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)}}(),function(){var t={143:0};s.O.j=function(e){return 0===t[e]};var e=function(e,i){var n,o,a=i[0],h=i[1],r=i[2],d=0;if(a.some((function(e){return 0!==t[e]}))){for(n in h)s.o(h,n)&&(s.m[n]=h[n]);if(r)var l=r(s)}for(e&&e(i);d<a.length;d++)o=a[d],s.o(t,o)&&t[o]&&t[o][0](),t[o]=0;return s.O(l)},i=self["webpackChunksnookertimer"]=self["webpackChunksnookertimer"]||[];i.forEach(e.bind(null,0)),i.push=e.bind(null,i.push.bind(i))}();var i=s.O(void 0,[998],(function(){return s(5702)}));i=s.O(i)})();
//# sourceMappingURL=app.fab49185.js.map