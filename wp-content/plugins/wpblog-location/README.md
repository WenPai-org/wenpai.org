# WordPress 博客 IP 地址属地插件（wpblog-location）
Display user account IP address information in comments and articles.


根据国家网信办，关于《互联网用户账号名称信息管理规定（征求意见稿）》公开征求意见的通知要求：

第十二条 互联网用户账号服务平台应当以显著方式，在互联网用户账号信息页面展示账号IP地址属地信息。境内互联网用户账号IP地址属地信息需标注到省（区、市），境外账号IP地址属地信息需标注到国家（地区）。

目前此插件除了默认在后台可以设置是否显示 IP 属地外，还提供了两个简码可自行调用以便在需要的位置进行显示：

可显示评论者 IP
[wpblog_post_location]

可显示作者 IP
[wpblog_author_location]

添加到需要的位置即可。

IP 地址数据库来自与 ipip net 的免费版本，时间停留在 2019 年，所以不是很准确，凑合着用吧，另外还添加了一个纯真的 IP 数据库备用，但都不是很准确，后续有好办法了再更新。
