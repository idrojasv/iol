��    [      �     �      �     �  @   �  $     z   5     �     �  �   �  f   x	     �	     �	  !   
     0
  .   P
     
  ^   �
  }   �
  �   w     �  #        /     K     Y     _     n     s  L   �  '   �  $   �  �     6   �     �     �  *   
  F   5  �   |  �   6  y   �  �   S  p   �  [   Z     �  @   �            >   5  
   t  j     o   �  #   Z     ~  q   �  �     ;   �     �       5        P     a     e     t     �     �     �  g   �  8   "     [  +   d     �  O   �  q   �     g  #   �  y   �  Q   %  �   w  P        _  B   k  o   �          1  �   D  !   �  �   �  �   �  9   w     �  !   �     �     �  &       9  >   @       �   �           1  �   R  ^   �     H      [   $   k       �   +   �      �   ]   �   p   T!  ~   �!     D"  !   T"     v"     �"     �"     �"     �"     �"  H   �"  '   #  !   >#  �   `#  B   �#     '$     8$  '   P$  N   x$  �   �$  |   d%  y   �%  �   [&  k   �&  i   S'     �'  B   �'  $   (     ,(  6   @(     w(  \   �(  ~   �(  +   `)     �)  m   �)  �   *  <   �*     �*     �*  0   +     <+     M+     Q+     `+     }+  	   �+     �+  l   �+  <   ,     K,      R,     s,  7   �,  d   �,     &-     D-  d   `-  U   �-  �   .  ^   �.  	   /  G   /  �   T/     �/     0  i   0     �0  {   �0  �   1  ;   �1     02  !   >2     `2     �2         G         <   &       F             0   @      V   "       9       6   H   N              !          /   (   E              P       D          4   Z   5      R       ;      ?      U   .   7       3   ,   #   1   J   2      	               %       '   O   Q          C              8       [   T       B   S   )          *             X   =   M                
       I               Y   A       W       +       $       -   :                K   >      L                      and  %s is a singleton class and you cannot create a second instance. .htaccess is currently not writable. .htaccess is not writable. Set 301 WordPress redirect, or set the .htaccess manually if you want to redirect in .htaccess. .htaccess redirect 301 redirect to https set:  <a href="https://en.wikipedia.org/wiki/HTTP_Strict_Transport_Security" target="_blank">HTTP Strict Transport Security</a> was not set in your .htaccess. A definition of a siteurl or homeurl was detected in your wp-config.php, but the file is not writable. Activate SSL networkwide Activate SSL per site Activate networkwide to fix this. Almost ready to migrate to SSL! An SSL certificate was detected on your site.  Auto replace mixed content Because the $_SERVER["HTTPS"] variable is not set, your website may experience redirect loops. Because your server does not pass a variable with which Wordpress can detect SSL, Wordpress may create redirect loops on SSL. Because your site is behind a loadbalancer and is_ssl() returns false, you should add the following line of code to your wp-config.php. Check again Check out Really Simple SSL Premium Choose your preferred setup Configuration Debug Detected setup Docs Documentation Don't forget to change your settings in Google Analytics en Webmaster tools. Enable WordPress 301 redirection to SSL Enable javascript redirection to ssl Enable this if you want to use the internal WordPress 301 redirect. Needed on NGINX servers, or if the .htaccess redirect cannot be used. Enable this option to get debug info in the debug tab. Failed activating SSL Go ahead, activate SSL! HTTP Strict Transport Security was enabled Http references in your .css and .js files: change any http:// into // Https redirect cannot be set in the .htaccess because the htaccess redirect rule could not be verified. Set the .htaccess redirect manually or enable WordPress redirect in the settings. Https redirect cannot be set in the .htaccess file because you have activated per site on a multiste subfolder install. Enable WordPress redirect in the settings. If you want to be sure you're ready to migrate to SSL, get Premium, which includes an extensive scan and premium support. If you want to customize the Really Simple SSL .htaccess, you need to prevent Really Simple SSL from rewriting it. Enabling this option will do that. Images, stylesheets or scripts from a domain without an ssl certificate: remove them or move to your own server. In most cases you need to leave this enabled, to prevent mixed content issues on your site. Instructions Lightweight plugin without any setup to make your site ssl proof Log for debugging purposes Major security issue! Mixed content fixer was successfully detected on the front-end More info. Networkwide activation does not check if a site has an SSL certificate. It just migrates all sites to SSL. No 301 redirect is set. Add a redirect to your nginx.conf, or enable the WordPress 301 redirect in the settings No SSL detected, but SSL is forced. No SSL detected. No SSL was detected. If you do have an ssl certificate, try to reload this page over https by clicking this link: On <a href='https://www.really-simple-ssl.com'>www.really-simple-ssl.com</a> you can find a lot of articles and documentation about installing this plugin, and installing SSL in general. Or set your wp-config.php to writable and reload this page. Premium Support Really Simple SSL Really Simple SSL has a conflict with another plugin. Rogier Lankhorst SSL SSL activated! SSL is enabled on your site. SSL is not enabled yet SSL settings Save Send me a copy of these lines if you have any issues. The log will be erased when debug is set to false Set your wp-config.php to writable and reload this page. Settings Settings to optimize your SSL configuration Show me this setting Some things can't be done automatically. Before you migrate, please check for:  Still having issues with mixed content? Check out Premium, which includes an extensive scan and premium support.  Stop editing the .htaccess file System detection encountered issues The 'force-deactivate.php' file has to be renamed to .txt. Otherwise your ssl can be deactived by anyone on the internet. The force http after leaving checkout in Woocommerce will create a redirect loop. The mixed content fixer is activated, but was not detected on the frontpage. Please follow these steps to check if the mixed content fixer is working. This is a fallback you should only use if other redirection methods do not work. To enable,  To view results here, enable the debug option in the settings tab. Try to add these rules above the wordpress lines in your .htaccess. If it doesn't work, just remove them again. View settings page WordPress redirect You can also let the automatic scan of the pro version handle this for you, and get premium support and increased security with HSTS included. You can check your certificate on You run a Multisite installation with subfolders, which prevents this plugin from fixing your missing server variable in the wp-config.php. Your .htaccess does not contain the Really Simple SSL redirect to https, and could not be written. For SEO purposes it is advisable to use 301 redirects. You can also use the internal WordPress 301 redirect, which can be enabled in the settings. Your wp-config.php has to be edited, but is not writable. get Premium https://www.really-simple-ssl.com https://www.rogierlankhorst.com reload over https. PO-Revision-Date: 2017-02-17 12:58:56+0000
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=1; plural=0;
X-Generator: GlotPress/2.3.0-alpha
Language: zh_CN
Project-Id-Version: Plugins - Really Simple SSL - Stable (latest release)
 以及 %s是一个单例级而你无法新增第二个执行个体。 .htaccess目前无法写。 .htaccess 无法被写。请设 301 WordPress 重导向， 或手动设定 .htaccess 若你要在 .htaccess 里重导向的话。 .htaccess 重导向 301 重导向到 https 设定； <a href="https://en.wikipedia.org/wiki/HTTP_Strict_Transport_Security" target="_blank">HTTP Strict Transport Security</a> 没有设在你的 .htaccess 一站点网站或在地网站在你的wp-config.php被侦测到，可是档案无法被写。 起动SSL全网路 启动每站SSL 启动广域网路来修复这个。 几乎准备好要迁移到 SSL! 有一 SSL认证在你网站被侦测到。 自动更换混合内容 因为这$_SERVER["HTTPS"] 变数尚未设好，你的网站可能会经历重导向回圈。 因为你的伺服器没有通过Wordpress可以侦测SSL的变数，Wordpress 可能在SSL新增重导向回圈 因为你的网站在负载平衡器的后面及 is_ssl() 返回假， 你应加入接下来的密码行到你wp-config.pho。 再检查一遍 查寻 Really Siomple SSL Premium 选择你想要的设定 配置 除错设定 被侦测到的设定 文档 程序文档 别忘记要更改你在Google分析及网路管理员工具里的设定 使 Word Press 301 重新导向 到 SSL 开启javascript 重导向至 ssl 若你想要使用内部的WordPress301导向请启动这个。但你需要在NGINX伺服器，或.htaccess导向无法使用时。 启用这选项以便在除错tab里取得除错程式的资料。 启动 SSL失败 动手吧，启动 SSL! HTTP 严密传输安全系统 被启用 在你的 .css 及 .js 档的 Http 参考文献: 改变所有的http:// 为 // Https导向无法被设在.htaccess里因为.htaccess导向规则无法被验证。请手动设定.htaccess导向或者在设定里启动 WordPress导向。 Https 重导向无法被设在.htaccess 档案里因为你已经启动了每一站在多重网站的子资料夹的安装。 如果你要被确定你已准备好迁移至SSL，请取得Premium，那有包括较广泛的扫瞄及优质的支援。 如果你要定制Really Simple SSL的.htaccess，你得防止真Really Simple SSL被重写。启动这选项使的.htaccess不被重写。 一没有SSL认证的网域上的图片，样式表或脚本: 移除掉或搬移它们到你的伺服器。 在大部分情况你得将它维持在启动状态，以防止在你网站上有混合内容的问题。 指示 轻量级的插件无需任何设定来让你的网站有ssl验证 以除错目的为用的记录 (log) 重大安全问题! 混合内容修复器已成功的在前端被侦测到 更多資訊 全网路开启并不会查证网站是否有 SSL 认证。它只是迁移整个站到SSL。 没有301重新导向被设定。加入一重导向到你的 nginx.conf ， 或者启动 在设定里的WordPress301 重导向 无 SSL被侦测，但SSL是被强制的。 无 SSL被侦测。 没有SSL被侦测到。如果你有一 ssl 认证，就试着点选这连结去重新加载这 https 网页: 通常你可以在<a href='https://www.really-simple-ssl.com'>www.really-simple-ssl.com</a>找到许多关于这插件，及安装SSL的文章及文件。 或设定你的 wp-config.php 为可写并重载这网页。 优质支援服務 Really Simple SSL Really Simple SSL 和另一个插件有冲突。 Rogier Lankhorst SSL SSL 已开启! SSL 已能在你网站使用 SSL仍未能使用 SSL设定 存档 若有任何问题就传给我这些字行的拷贝。当除错工具被设为 false 时 log 会被消除掉 或设定你的 wp-config.php 为可写并重载这网页。 設定 设定以优化你的 SSL 配置 向我显示这设定 有些无法自动被完成。在迁移前，先查寻: 还是有混合内容的问题吗? 查看 Premium，那有包括一广泛的扫瞄及优质支援。 停止编辑 .htaccess 档案 系统侦测碰到的问题 'force-deactivate.php' 档必须更名为.txt. 不然你的 ssl 会被网路上其他人侦测到。 在离开 Woocommerce 的线上付款后强力的 http 会新增一重导向回圈。 混合内容修复器已经开启，但没有在首页被侦测到，请按照这些步骤看看这混合内容修复器是否运作。 这是一个 fallback 你应该只有在如果其它重新导向方式无法作用时使用。 启动， 要看这里结果，请启动在设定 tab 里的除错工具选项。 试着在你.htaccess 中的 wordpress 字行之上里加入这些规则。如果它不起作用，就只要将它们就只要将它们再删除一次就好了。 看设定页 WordPress 重导向 你也可以让进阶版本的自动扫瞄帮你处理，并取得优质支援及增加保全包括 HSTS. 你可以查看你的认证在 你用子资料夹去执行多站点的安装，会阻止这插件修复你在 wp-config.php 里遗失的伺服器变数。 你的.htaccess没有包含Really Simple SSL到 https 的导向，也无法被写。为了SEO目的是建议可以使用 301重导。你也可以使用内部 WordPress 301 导向，这可以在设定里被启动。 你的wp-config.php 必须要编辑，可是并非可写。 取得Premium https://www.really-simple-ssl.com https://www.rogierlankhorst.com 重新加载 https. 