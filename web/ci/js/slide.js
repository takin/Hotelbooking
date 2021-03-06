;(function($j){var ver='2.65';if($j.support==undefined){$j.support={opacity:!($j.browser.msie)};}
function log(){if(window.console&&window.console.log)
window.console.log('[cycle] '+Array.prototype.join.call(arguments,' '));};$j.fn.cycle=function(options,arg2){var o={s:this.selector,c:this.context};if(this.length==0&&options!='stop'){if(!$j.isReady&&o.s){log('DOM not ready, queuing slideshow')
$j(function(){$j(o.s,o.c).cycle(options,arg2);});return this;}
log('terminating; zero elements found by selector'+($j.isReady?'':' (DOM not ready)'));return this;}
return this.each(function(){options=handleArguments(this,options,arg2);if(options===false)
return;if(this.cycleTimeout)
clearTimeout(this.cycleTimeout);this.cycleTimeout=this.cyclePause=0;var $jcont=$j(this);var $jslides=options.slideExpr?$j(options.slideExpr,this):$jcont.children();var els=$jslides.get();if(els.length<2){log('terminating; too few slides: '+els.length);return;}
var opts=buildOptions($jcont,$jslides,els,options,o);if(opts===false)
return;if(opts.timeout||opts.continuous)
this.cycleTimeout=setTimeout(function(){go(els,opts,0,!opts.rev)},opts.continuous?10:opts.timeout+(opts.delay||0));});};function handleArguments(cont,options,arg2){if(cont.cycleStop==undefined)
cont.cycleStop=0;if(options===undefined||options===null)
options={};if(options.constructor==String){switch(options){case'stop':cont.cycleStop++;if(cont.cycleTimeout)
clearTimeout(cont.cycleTimeout);cont.cycleTimeout=0;$j(cont).removeData('cycle.opts');return false;case'pause':cont.cyclePause=1;return false;case'resume':cont.cyclePause=0;if(arg2===true){options=$j(cont).data('cycle.opts');if(!options){log('options not found, can not resume');return false;}
if(cont.cycleTimeout){clearTimeout(cont.cycleTimeout);cont.cycleTimeout=0;}
go(options.elements,options,1,1);}
return false;default:options={fx:options};};}
else if(options.constructor==Number){var num=options;options=$j(cont).data('cycle.opts');if(!options){log('options not found, can not advance slide');return false;}
if(num<0||num>=options.elements.length){log('invalid slide index: '+num);return false;}
options.nextSlide=num;if(cont.cycleTimeout){clearTimeout(cont.cycleTimeout);cont.cycleTimeout=0;}
if(typeof arg2=='string')
options.oneTimeFx=arg2;go(options.elements,options,1,num>=options.currSlide);return false;}
return options;};function removeFilter(el,opts){if(!$j.support.opacity&&opts.cleartype&&el.style.filter){try{el.style.removeAttribute('filter');}
catch(smother){}}};function buildOptions($jcont,$jslides,els,options,o){var opts=$j.extend({},$j.fn.cycle.defaults,options||{},$j.metadata?$jcont.metadata():$j.meta?$jcont.data():{});if(opts.autostop)
opts.countdown=opts.autostopCount||els.length;var cont=$jcont[0];$jcont.data('cycle.opts',opts);opts.$jcont=$jcont;opts.stopCount=cont.cycleStop;opts.elements=els;opts.before=opts.before?[opts.before]:[];opts.after=opts.after?[opts.after]:[];opts.after.unshift(function(){opts.busy=0;});if(!$j.support.opacity&&opts.cleartype)
opts.after.push(function(){removeFilter(this,opts);});if(opts.continuous)
opts.after.push(function(){go(els,opts,0,!opts.rev);});saveOriginalOpts(opts);if(!$j.support.opacity&&opts.cleartype&&!opts.cleartypeNoBg)
clearTypeFix($jslides);if($jcont.css('position')=='static')
$jcont.css('position','relative');if(opts.width)
$jcont.width(opts.width);if(opts.height&&opts.height!='auto')
$jcont.height(opts.height);if(opts.startingSlide)
opts.startingSlide=parseInt(opts.startingSlide);if(opts.random){opts.randomMap=[];for(var i=0;i<els.length;i++)
opts.randomMap.push(i);opts.randomMap.sort(function(a,b){return Math.random()-0.5;});opts.randomIndex=0;opts.startingSlide=opts.randomMap[0];}
else if(opts.startingSlide>=els.length)
opts.startingSlide=0;opts.currSlide=opts.startingSlide=opts.startingSlide||0;var first=opts.startingSlide;$jslides.css({position:'absolute',top:0,left:0}).hide().each(function(i){var z=first?i>=first?els.length-(i-first):first-i:els.length-i;$j(this).css('z-index',z)});$j(els[first]).css('opacity',1).show();removeFilter(els[first],opts);if(opts.fit&&opts.width)
$jslides.width(opts.width);if(opts.fit&&opts.height&&opts.height!='auto')
$jslides.height(opts.height);var reshape=opts.containerResize&&!$jcont.innerHeight();if(reshape){var maxw=0,maxh=0;for(var i=0;i<els.length;i++){var $je=$j(els[i]),e=$je[0],w=$je.outerWidth(),h=$je.outerHeight();if(!w)w=e.offsetWidth;if(!h)h=e.offsetHeight;maxw=w>maxw?w:maxw;maxh=h>maxh?h:maxh;}
if(maxw>0&&maxh>0)
$jcont.css({width:maxw+'px',height:maxh+'px'});}
if(opts.pause)
$jcont.hover(function(){this.cyclePause++;},function(){this.cyclePause--;});if(supportMultiTransitions(opts)===false)
return false;if(!opts.multiFx){var init=$j.fn.cycle.transitions[opts.fx];if($j.isFunction(init))
init($jcont,$jslides,opts);else if(opts.fx!='custom'&&!opts.multiFx){log('unknown transition: '+opts.fx,'; slideshow terminating');return false;}}
var requeue=false;options.requeueAttempts=options.requeueAttempts||0;$jslides.each(function(){var $jel=$j(this);this.cycleH=(opts.fit&&opts.height)?opts.height:$jel.height();this.cycleW=(opts.fit&&opts.width)?opts.width:$jel.width();if($jel.is('img')){var loadingIE=($j.browser.msie&&this.cycleW==28&&this.cycleH==30&&!this.complete);var loadingOp=($j.browser.opera&&this.cycleW==42&&this.cycleH==19&&!this.complete);var loadingOther=(this.cycleH==0&&this.cycleW==0&&!this.complete);if(loadingIE||loadingOp||loadingOther){if(o.s&&opts.requeueOnImageNotLoaded&&++options.requeueAttempts<100){log(options.requeueAttempts,' - img slide not loaded, requeuing slideshow: ',this.src,this.cycleW,this.cycleH);setTimeout(function(){$j(o.s,o.c).cycle(options)},opts.requeueTimeout);requeue=true;return false;}
else{log('could not determine size of image: '+this.src,this.cycleW,this.cycleH);}}}
return true;});if(requeue)
return false;opts.cssBefore=opts.cssBefore||{};opts.animIn=opts.animIn||{};opts.animOut=opts.animOut||{};$jslides.not(':eq('+first+')').css(opts.cssBefore);if(opts.cssFirst)
$j($jslides[first]).css(opts.cssFirst);if(opts.timeout){opts.timeout=parseInt(opts.timeout);if(opts.speed.constructor==String)
opts.speed=$j.fx.speeds[opts.speed]||parseInt(opts.speed);if(!opts.sync)
opts.speed=opts.speed/2;while((opts.timeout-opts.speed)<250)
opts.timeout+=opts.speed;}
if(opts.easing)
opts.easeIn=opts.easeOut=opts.easing;if(!opts.speedIn)
opts.speedIn=opts.speed;if(!opts.speedOut)
opts.speedOut=opts.speed;opts.slideCount=els.length;opts.currSlide=opts.lastSlide=first;if(opts.random){opts.nextSlide=opts.currSlide;if(++opts.randomIndex==els.length)
opts.randomIndex=0;opts.nextSlide=opts.randomMap[opts.randomIndex];}
else
opts.nextSlide=opts.startingSlide>=(els.length-1)?0:opts.startingSlide+1;var e0=$jslides[first];if(opts.before.length)
opts.before[0].apply(e0,[e0,e0,opts,true]);if(opts.after.length>1)
opts.after[1].apply(e0,[e0,e0,opts,true]);if(opts.next)
$j(opts.next).click(function(){return advance(opts,opts.rev?-1:1)});if(opts.prev)
$j(opts.prev).click(function(){return advance(opts,opts.rev?1:-1)});if(opts.pager)
buildPager(els,opts);exposeAddSlide(opts,els);return opts;};function saveOriginalOpts(opts){opts.original={before:[],after:[]};opts.original.cssBefore=$j.extend({},opts.cssBefore);opts.original.cssAfter=$j.extend({},opts.cssAfter);opts.original.animIn=$j.extend({},opts.animIn);opts.original.animOut=$j.extend({},opts.animOut);$j.each(opts.before,function(){opts.original.before.push(this);});$j.each(opts.after,function(){opts.original.after.push(this);});};function supportMultiTransitions(opts){var txs=$j.fn.cycle.transitions;if(opts.fx.indexOf(',')>0){opts.multiFx=true;opts.fxs=opts.fx.replace(/\s*/g,'').split(',');for(var i=0;i<opts.fxs.length;i++){var fx=opts.fxs[i];var tx=txs[fx];if(!tx||!txs.hasOwnProperty(fx)||!$j.isFunction(tx)){log('discarding unknown transition: ',fx);opts.fxs.splice(i,1);i--;}}
if(!opts.fxs.length){log('No valid transitions named; slideshow terminating.');return false;}}
else if(opts.fx=='all'){opts.multiFx=true;opts.fxs=[];for(p in txs){var tx=txs[p];if(txs.hasOwnProperty(p)&&$j.isFunction(tx))
opts.fxs.push(p);}}
if(opts.multiFx&&opts.randomizeEffects){var r1=Math.floor(Math.random()*20)+30;for(var i=0;i<r1;i++){var r2=Math.floor(Math.random()*opts.fxs.length);opts.fxs.push(opts.fxs.splice(r2,1)[0]);}
log('randomized fx sequence: ',opts.fxs);}
return true;};function exposeAddSlide(opts,els){opts.addSlide=function(newSlide,prepend){var $js=$j(newSlide),s=$js[0];if(!opts.autostopCount)
opts.countdown++;els[prepend?'unshift':'push'](s);if(opts.els)
opts.els[prepend?'unshift':'push'](s);opts.slideCount=els.length;$js.css('position','absolute');$js[prepend?'prependTo':'appendTo'](opts.$jcont);if(prepend){opts.currSlide++;opts.nextSlide++;}
if(!$j.support.opacity&&opts.cleartype&&!opts.cleartypeNoBg)
clearTypeFix($js);if(opts.fit&&opts.width)
$js.width(opts.width);if(opts.fit&&opts.height&&opts.height!='auto')
$jslides.height(opts.height);s.cycleH=(opts.fit&&opts.height)?opts.height:$js.height();s.cycleW=(opts.fit&&opts.width)?opts.width:$js.width();$js.css(opts.cssBefore);if(opts.pager)
$j.fn.cycle.createPagerAnchor(els.length-1,s,$j(opts.pager),els,opts);if($j.isFunction(opts.onAddSlide))
opts.onAddSlide($js);else
$js.hide();};}
$j.fn.cycle.resetState=function(opts,fx){fx=fx||opts.fx;opts.before=[];opts.after=[];opts.cssBefore=$j.extend({},opts.original.cssBefore);opts.cssAfter=$j.extend({},opts.original.cssAfter);opts.animIn=$j.extend({},opts.original.animIn);opts.animOut=$j.extend({},opts.original.animOut);opts.fxFn=null;$j.each(opts.original.before,function(){opts.before.push(this);});$j.each(opts.original.after,function(){opts.after.push(this);});var init=$j.fn.cycle.transitions[fx];if($j.isFunction(init))
init(opts.$jcont,$j(opts.elements),opts);};function go(els,opts,manual,fwd){if(manual&&opts.busy&&opts.manualTrump){$j(els).stop(true,true);opts.busy=false;}
if(opts.busy)
return;var p=opts.$jcont[0],curr=els[opts.currSlide],next=els[opts.nextSlide];if(p.cycleStop!=opts.stopCount||p.cycleTimeout===0&&!manual)
return;if(!manual&&!p.cyclePause&&((opts.autostop&&(--opts.countdown<=0))||(opts.nowrap&&!opts.random&&opts.nextSlide<opts.currSlide))){if(opts.end)
opts.end(opts);return;}
if(manual||!p.cyclePause){var fx=opts.fx;curr.cycleH=curr.cycleH||$j(curr).height();curr.cycleW=curr.cycleW||$j(curr).width();next.cycleH=next.cycleH||$j(next).height();next.cycleW=next.cycleW||$j(next).width();if(opts.multiFx){if(opts.lastFx==undefined||++opts.lastFx>=opts.fxs.length)
opts.lastFx=0;fx=opts.fxs[opts.lastFx];opts.currFx=fx;}
if(opts.oneTimeFx){fx=opts.oneTimeFx;opts.oneTimeFx=null;}
$j.fn.cycle.resetState(opts,fx);if(opts.before.length)
$j.each(opts.before,function(i,o){if(p.cycleStop!=opts.stopCount)return;o.apply(next,[curr,next,opts,fwd]);});var after=function(){$j.each(opts.after,function(i,o){if(p.cycleStop!=opts.stopCount)return;o.apply(next,[curr,next,opts,fwd]);});};if(opts.nextSlide!=opts.currSlide){opts.busy=1;if(opts.fxFn)
opts.fxFn(curr,next,opts,after,fwd);else if($j.isFunction($j.fn.cycle[opts.fx]))
$j.fn.cycle[opts.fx](curr,next,opts,after);else
$j.fn.cycle.custom(curr,next,opts,after,manual&&opts.fastOnEvent);}
opts.lastSlide=opts.currSlide;if(opts.random){opts.currSlide=opts.nextSlide;if(++opts.randomIndex==els.length)
opts.randomIndex=0;opts.nextSlide=opts.randomMap[opts.randomIndex];}
else{var roll=(opts.nextSlide+1)==els.length;opts.nextSlide=roll?0:opts.nextSlide+1;opts.currSlide=roll?els.length-1:opts.nextSlide-1;}
if(opts.pager)
$j.fn.cycle.updateActivePagerLink(opts.pager,opts.currSlide);}
var ms=0;if(opts.timeout&&!opts.continuous)
ms=getTimeout(curr,next,opts,fwd);else if(opts.continuous&&p.cyclePause)
ms=10;if(ms>0)
p.cycleTimeout=setTimeout(function(){go(els,opts,0,!opts.rev)},ms);};$j.fn.cycle.updateActivePagerLink=function(pager,currSlide){$j(pager).find('a').removeClass('activeSlide').stop().animate({opacity:0.5},500).filter('a:eq('+currSlide+')').addClass('activeSlide').stop().animate({opacity:1},500);};function getTimeout(curr,next,opts,fwd){if(opts.timeoutFn){var t=opts.timeoutFn(curr,next,opts,fwd);if(t!==false)
return t;}
return opts.timeout;};$j.fn.cycle.next=function(opts){advance(opts,opts.rev?-1:1);};$j.fn.cycle.prev=function(opts){advance(opts,opts.rev?1:-1);};function advance(opts,val){var els=opts.elements;var p=opts.$jcont[0],timeout=p.cycleTimeout;if(timeout){clearTimeout(timeout);p.cycleTimeout=0;}
if(opts.random&&val<0){opts.randomIndex--;if(--opts.randomIndex==-2)
opts.randomIndex=els.length-2;else if(opts.randomIndex==-1)
opts.randomIndex=els.length-1;opts.nextSlide=opts.randomMap[opts.randomIndex];}
else if(opts.random){if(++opts.randomIndex==els.length)
opts.randomIndex=0;opts.nextSlide=opts.randomMap[opts.randomIndex];}
else{opts.nextSlide=opts.currSlide+val;if(opts.nextSlide<0){if(opts.nowrap)return false;opts.nextSlide=els.length-1;}
else if(opts.nextSlide>=els.length){if(opts.nowrap)return false;opts.nextSlide=0;}}
if($j.isFunction(opts.prevNextClick))
opts.prevNextClick(val>0,opts.nextSlide,els[opts.nextSlide]);go(els,opts,1,val>=0);return false;};function buildPager(els,opts){var $jp=$j(opts.pager);$j.each(els,function(i,o){$j.fn.cycle.createPagerAnchor(i,o,$jp,els,opts);});$j.fn.cycle.updateActivePagerLink(opts.pager,opts.startingSlide);};$j.fn.cycle.createPagerAnchor=function(i,el,$jp,els,opts){var a=($j.isFunction(opts.pagerAnchorBuilder))?opts.pagerAnchorBuilder(i,el):'<a href="#">'+(i+1)+'</a>';if(!a)
return;var $ja=$j(a);if($ja.parents('body').length==0){var arr=[];if($jp.length>1){$jp.each(function(){var $jclone=$ja.clone(true);$j(this).append($jclone);arr.push($jclone);});$ja=$j(arr);}
else{$ja.appendTo($jp);}}
$ja.bind(opts.pagerEvent,function(){opts.nextSlide=i;var p=opts.$jcont[0],timeout=p.cycleTimeout;if(timeout){clearTimeout(timeout);p.cycleTimeout=0;}
if($j.isFunction(opts.pagerClick))
opts.pagerClick(opts.nextSlide,els[opts.nextSlide]);go(els,opts,1,opts.currSlide<i);return false;});if(opts.pauseOnPagerHover)
$ja.hover(function(){opts.$jcont[0].cyclePause++;},function(){opts.$jcont[0].cyclePause--;});};$j.fn.cycle.hopsFromLast=function(opts,fwd){var hops,l=opts.lastSlide,c=opts.currSlide;if(fwd)
hops=c>l?c-l:opts.slideCount-l;else
hops=c<l?l-c:l+opts.slideCount-c;return hops;};function clearTypeFix($jslides){function hex(s){s=parseInt(s).toString(16);return s.length<2?'0'+s:s;};function getBg(e){for(;e&&e.nodeName.toLowerCase()!='html';e=e.parentNode){var v=$j.css(e,'background-color');if(v.indexOf('rgb')>=0){var rgb=v.match(/\d+/g);return'#'+hex(rgb[0])+hex(rgb[1])+hex(rgb[2]);}
if(v&&v!='transparent')
return v;}
return'#ffffff';};$jslides.each(function(){$j(this).css('background-color',getBg(this));});};$j.fn.cycle.commonReset=function(curr,next,opts,w,h,rev){$j(opts.elements).not(curr).hide();opts.cssBefore.opacity=1;opts.cssBefore.display='block';if(w!==false&&next.cycleW>0)
opts.cssBefore.width=next.cycleW;if(h!==false&&next.cycleH>0)
opts.cssBefore.height=next.cycleH;opts.cssAfter=opts.cssAfter||{};opts.cssAfter.display='none';$j(curr).css('zIndex',opts.slideCount+(rev===true?1:0));$j(next).css('zIndex',opts.slideCount+(rev===true?0:1));};$j.fn.cycle.custom=function(curr,next,opts,cb,speedOverride){var $jl=$j(curr),$jn=$j(next);var speedIn=opts.speedIn,speedOut=opts.speedOut,easeIn=opts.easeIn,easeOut=opts.easeOut;$jn.css(opts.cssBefore);if(speedOverride){if(typeof speedOverride=='number')
speedIn=speedOut=speedOverride;else
speedIn=speedOut=1;easeIn=easeOut=null;}
var fn=function(){$jn.animate(opts.animIn,speedIn,easeIn,cb)};$jl.animate(opts.animOut,speedOut,easeOut,function(){if(opts.cssAfter)$jl.css(opts.cssAfter);if(!opts.sync)fn();});if(opts.sync)fn();};$j.fn.cycle.transitions={fade:function($jcont,$jslides,opts){$jslides.not(':eq('+opts.currSlide+')').css('opacity',0);opts.before.push(function(curr,next,opts){$j.fn.cycle.commonReset(curr,next,opts);opts.cssBefore.opacity=0;});opts.animIn={opacity:1};opts.animOut={opacity:0};opts.cssBefore={top:0,left:0};}};$j.fn.cycle.ver=function(){return ver;};$j.fn.cycle.defaults={fx:'fade',timeout:4000,timeoutFn:null,continuous:0,speed:1000,speedIn:null,speedOut:null,next:null,prev:null,prevNextClick:null,pager:null,pagerClick:null,pagerEvent:'click',pagerAnchorBuilder:null,before:null,after:null,end:null,easing:null,easeIn:null,easeOut:null,shuffle:null,animIn:null,animOut:null,cssBefore:null,cssAfter:null,fxFn:null,height:'auto',startingSlide:0,sync:1,random:0,fit:0,containerResize:1,pause:0,pauseOnPagerHover:0,autostop:0,autostopCount:0,delay:0,slideExpr:null,cleartype:!$j.support.opacity,nowrap:0,fastOnEvent:0,randomizeEffects:1,rev:0,manualTrump:true,requeueOnImageNotLoaded:true,requeueTimeout:250};})(jQuery);(function($j){$j.fn.cycle.transitions.scrollUp=function($jcont,$jslides,opts){$jcont.css('overflow','hidden');opts.before.push($j.fn.cycle.commonReset);var h=$jcont.height();opts.cssBefore={top:h,left:0};opts.cssFirst={top:0};opts.animIn={top:0};opts.animOut={top:-h};};$j.fn.cycle.transitions.scrollDown=function($jcont,$jslides,opts){$jcont.css('overflow','hidden');opts.before.push($j.fn.cycle.commonReset);var h=$jcont.height();opts.cssFirst={top:0};opts.cssBefore={top:-h,left:0};opts.animIn={top:0};opts.animOut={top:h};};$j.fn.cycle.transitions.scrollLeft=function($jcont,$jslides,opts){$jcont.css('overflow','hidden');opts.before.push($j.fn.cycle.commonReset);var w=$jcont.width();opts.cssFirst={left:0};opts.cssBefore={left:w,top:0};opts.animIn={left:0};opts.animOut={left:0-w};};$j.fn.cycle.transitions.scrollRight=function($jcont,$jslides,opts){$jcont.css('overflow','hidden');opts.before.push($j.fn.cycle.commonReset);var w=$jcont.width();opts.cssFirst={left:0};opts.cssBefore={left:-w,top:0};opts.animIn={left:0};opts.animOut={left:w};};$j.fn.cycle.transitions.scrollHorz=function($jcont,$jslides,opts){$jcont.css('overflow','hidden').width();opts.before.push(function(curr,next,opts,fwd){$j.fn.cycle.commonReset(curr,next,opts);opts.cssBefore.left=fwd?(next.cycleW-1):(1-next.cycleW);opts.animOut.left=fwd?-curr.cycleW:curr.cycleW;});opts.cssFirst={left:0};opts.cssBefore={top:0};opts.animIn={left:0};opts.animOut={top:0};};$j.fn.cycle.transitions.scrollVert=function($jcont,$jslides,opts){$jcont.css('overflow','hidden');opts.before.push(function(curr,next,opts,fwd){$j.fn.cycle.commonReset(curr,next,opts);opts.cssBefore.top=fwd?(1-next.cycleH):(next.cycleH-1);opts.animOut.top=fwd?curr.cycleH:-curr.cycleH;});opts.cssFirst={top:0};opts.cssBefore={left:0};opts.animIn={top:0};opts.animOut={left:0};};$j.fn.cycle.transitions.slideX=function($jcont,$jslides,opts){opts.before.push(function(curr,next,opts){$j(opts.elements).not(curr).hide();$j.fn.cycle.commonReset(curr,next,opts,false,true);opts.animIn.width=next.cycleW;});opts.cssBefore={left:0,top:0,width:0};opts.animIn={width:'show'};opts.animOut={width:0};};$j.fn.cycle.transitions.slideY=function($jcont,$jslides,opts){opts.before.push(function(curr,next,opts){$j(opts.elements).not(curr).hide();$j.fn.cycle.commonReset(curr,next,opts,true,false);opts.animIn.height=next.cycleH;});opts.cssBefore={left:0,top:0,height:0};opts.animIn={height:'show'};opts.animOut={height:0};};$j.fn.cycle.transitions.shuffle=function($jcont,$jslides,opts){var w=$jcont.css('overflow','visible').width();$jslides.css({left:0,top:0});opts.before.push(function(curr,next,opts){$j.fn.cycle.commonReset(curr,next,opts,true,true,true);});opts.speed=opts.speed/2;opts.random=0;opts.shuffle=opts.shuffle||{left:-w,top:15};opts.els=[];for(var i=0;i<$jslides.length;i++)
opts.els.push($jslides[i]);for(var i=0;i<opts.currSlide;i++)
opts.els.push(opts.els.shift());opts.fxFn=function(curr,next,opts,cb,fwd){var $jel=fwd?$j(curr):$j(next);$j(next).css(opts.cssBefore);var count=opts.slideCount;$jel.animate(opts.shuffle,opts.speedIn,opts.easeIn,function(){var hops=$j.fn.cycle.hopsFromLast(opts,fwd);for(var k=0;k<hops;k++)
fwd?opts.els.push(opts.els.shift()):opts.els.unshift(opts.els.pop());if(fwd)
for(var i=0,len=opts.els.length;i<len;i++)
$j(opts.els[i]).css('z-index',len-i+count);else{var z=$j(curr).css('z-index');$jel.css('z-index',parseInt(z)+1+count);}
$jel.animate({left:0,top:0},opts.speedOut,opts.easeOut,function(){$j(fwd?this:curr).hide();if(cb)cb();});});};opts.cssBefore={display:'block',opacity:1,top:0,left:0};};$j.fn.cycle.transitions.turnUp=function($jcont,$jslides,opts){opts.before.push(function(curr,next,opts){$j.fn.cycle.commonReset(curr,next,opts,true,false);opts.cssBefore.top=next.cycleH;opts.animIn.height=next.cycleH;});opts.cssFirst={top:0};opts.cssBefore={left:0,height:0};opts.animIn={top:0};opts.animOut={height:0};};$j.fn.cycle.transitions.turnDown=function($jcont,$jslides,opts){opts.before.push(function(curr,next,opts){$j.fn.cycle.commonReset(curr,next,opts,true,false);opts.animIn.height=next.cycleH;opts.animOut.top=curr.cycleH;});opts.cssFirst={top:0};opts.cssBefore={left:0,top:0,height:0};opts.animOut={height:0};};$j.fn.cycle.transitions.turnLeft=function($jcont,$jslides,opts){opts.before.push(function(curr,next,opts){$j.fn.cycle.commonReset(curr,next,opts,false,true);opts.cssBefore.left=next.cycleW;opts.animIn.width=next.cycleW;});opts.cssBefore={top:0,width:0};opts.animIn={left:0};opts.animOut={width:0};};$j.fn.cycle.transitions.turnRight=function($jcont,$jslides,opts){opts.before.push(function(curr,next,opts){$j.fn.cycle.commonReset(curr,next,opts,false,true);opts.animIn.width=next.cycleW;opts.animOut.left=curr.cycleW;});opts.cssBefore={top:0,left:0,width:0};opts.animIn={left:0};opts.animOut={width:0};};$j.fn.cycle.transitions.zoom=function($jcont,$jslides,opts){opts.before.push(function(curr,next,opts){$j.fn.cycle.commonReset(curr,next,opts,false,false,true);opts.cssBefore.top=next.cycleH/2;opts.cssBefore.left=next.cycleW/2;opts.animIn={top:0,left:0,width:next.cycleW,height:next.cycleH};opts.animOut={width:0,height:0,top:curr.cycleH/2,left:curr.cycleW/2};});opts.cssFirst={top:0,left:0};opts.cssBefore={width:0,height:0};};$j.fn.cycle.transitions.fadeZoom=function($jcont,$jslides,opts){opts.before.push(function(curr,next,opts){$j.fn.cycle.commonReset(curr,next,opts,false,false);opts.cssBefore.left=next.cycleW/2;opts.cssBefore.top=next.cycleH/2;opts.animIn={top:0,left:0,width:next.cycleW,height:next.cycleH};});opts.cssBefore={width:0,height:0};opts.animOut={opacity:0};};$j.fn.cycle.transitions.blindX=function($jcont,$jslides,opts){var w=$jcont.css('overflow','hidden').width();opts.before.push(function(curr,next,opts){$j.fn.cycle.commonReset(curr,next,opts);opts.animIn.width=next.cycleW;opts.animOut.left=curr.cycleW;});opts.cssBefore={left:w,top:0};opts.animIn={left:0};opts.animOut={left:w};};$j.fn.cycle.transitions.blindY=function($jcont,$jslides,opts){var h=$jcont.css('overflow','hidden').height();opts.before.push(function(curr,next,opts){$j.fn.cycle.commonReset(curr,next,opts);opts.animIn.height=next.cycleH;opts.animOut.top=curr.cycleH;});opts.cssBefore={top:h,left:0};opts.animIn={top:0};opts.animOut={top:h};};$j.fn.cycle.transitions.blindZ=function($jcont,$jslides,opts){var h=$jcont.css('overflow','hidden').height();var w=$jcont.width();opts.before.push(function(curr,next,opts){$j.fn.cycle.commonReset(curr,next,opts);opts.animIn.height=next.cycleH;opts.animOut.top=curr.cycleH;});opts.cssBefore={top:h,left:w};opts.animIn={top:0,left:0};opts.animOut={top:h,left:w};};$j.fn.cycle.transitions.growX=function($jcont,$jslides,opts){opts.before.push(function(curr,next,opts){$j.fn.cycle.commonReset(curr,next,opts,false,true);opts.cssBefore.left=this.cycleW/2;opts.animIn={left:0,width:this.cycleW};opts.animOut={left:0};});opts.cssBefore={width:0,top:0};};$j.fn.cycle.transitions.growY=function($jcont,$jslides,opts){opts.before.push(function(curr,next,opts){$j.fn.cycle.commonReset(curr,next,opts,true,false);opts.cssBefore.top=this.cycleH/2;opts.animIn={top:0,height:this.cycleH};opts.animOut={top:0};});opts.cssBefore={height:0,left:0};};$j.fn.cycle.transitions.curtainX=function($jcont,$jslides,opts){opts.before.push(function(curr,next,opts){$j.fn.cycle.commonReset(curr,next,opts,false,true,true);opts.cssBefore.left=next.cycleW/2;opts.animIn={left:0,width:this.cycleW};opts.animOut={left:curr.cycleW/2,width:0};});opts.cssBefore={top:0,width:0};};$j.fn.cycle.transitions.curtainY=function($jcont,$jslides,opts){opts.before.push(function(curr,next,opts){$j.fn.cycle.commonReset(curr,next,opts,true,false,true);opts.cssBefore.top=next.cycleH/2;opts.animIn={top:0,height:next.cycleH};opts.animOut={top:curr.cycleH/2,height:0};});opts.cssBefore={left:0,height:0};};$j.fn.cycle.transitions.cover=function($jcont,$jslides,opts){var d=opts.direction||'left';var w=$jcont.css('overflow','hidden').width();var h=$jcont.height();opts.before.push(function(curr,next,opts){$j.fn.cycle.commonReset(curr,next,opts);if(d=='right')
opts.cssBefore.left=-w;else if(d=='up')
opts.cssBefore.top=h;else if(d=='down')
opts.cssBefore.top=-h;else
opts.cssBefore.left=w;});opts.animIn={left:0,top:0};opts.animOut={opacity:1};opts.cssBefore={top:0,left:0};};$j.fn.cycle.transitions.uncover=function($jcont,$jslides,opts){var d=opts.direction||'left';var w=$jcont.css('overflow','hidden').width();var h=$jcont.height();opts.before.push(function(curr,next,opts){$j.fn.cycle.commonReset(curr,next,opts,true,true,true);if(d=='right')
opts.animOut.left=w;else if(d=='up')
opts.animOut.top=-h;else if(d=='down')
opts.animOut.top=h;else
opts.animOut.left=-w;});opts.animIn={left:0,top:0};opts.animOut={opacity:1};opts.cssBefore={top:0,left:0};};$j.fn.cycle.transitions.toss=function($jcont,$jslides,opts){var w=$jcont.css('overflow','visible').width();var h=$jcont.height();opts.before.push(function(curr,next,opts){$j.fn.cycle.commonReset(curr,next,opts,true,true,true);if(!opts.animOut.left&&!opts.animOut.top)
opts.animOut={left:w*2,top:-h/2,opacity:0};else
opts.animOut.opacity=0;});opts.cssBefore={left:0,top:0};opts.animIn={left:0};};$j.fn.cycle.transitions.wipe=function($jcont,$jslides,opts){var w=$jcont.css('overflow','hidden').width();var h=$jcont.height();opts.cssBefore=opts.cssBefore||{};var clip;if(opts.clip){if(/l2r/.test(opts.clip))
clip='rect(0px 0px '+h+'px 0px)';else if(/r2l/.test(opts.clip))
clip='rect(0px '+w+'px '+h+'px '+w+'px)';else if(/t2b/.test(opts.clip))
clip='rect(0px '+w+'px 0px 0px)';else if(/b2t/.test(opts.clip))
clip='rect('+h+'px '+w+'px '+h+'px 0px)';else if(/zoom/.test(opts.clip)){var t=parseInt(h/2);var l=parseInt(w/2);clip='rect('+t+'px '+l+'px '+t+'px '+l+'px)';}}
opts.cssBefore.clip=opts.cssBefore.clip||clip||'rect(0px 0px 0px 0px)';var d=opts.cssBefore.clip.match(/(\d+)/g);var t=parseInt(d[0]),r=parseInt(d[1]),b=parseInt(d[2]),l=parseInt(d[3]);opts.before.push(function(curr,next,opts){if(curr==next)return;var $jcurr=$j(curr),$jnext=$j(next);$j.fn.cycle.commonReset(curr,next,opts,true,true,false);opts.cssAfter.display='block';var step=1,count=parseInt((opts.speedIn/13))-1;(function f(){var tt=t?t-parseInt(step*(t/count)):0;var ll=l?l-parseInt(step*(l/count)):0;var bb=b<h?b+parseInt(step*((h-b)/count||1)):h;var rr=r<w?r+parseInt(step*((w-r)/count||1)):w;$jnext.css({clip:'rect('+tt+'px '+rr+'px '+bb+'px '+ll+'px)'});(step++<=count)?setTimeout(f,13):$jcurr.css('display','none');})();});opts.cssBefore={display:'block',opacity:1,top:0,left:0};opts.animIn={left:0};opts.animOut={left:0};};})(jQuery);