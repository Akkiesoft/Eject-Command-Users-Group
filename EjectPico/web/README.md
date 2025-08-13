# Apache2での設定例

```
RewriteEngine On
RewriteCond %{HTTP:Connection} Upgrade [NC]
RewriteCond %{HTTP:Upgrade} websocket [NC]
RewriteRule /pico/ws ws://127.0.0.1:3981 [P,L]
```