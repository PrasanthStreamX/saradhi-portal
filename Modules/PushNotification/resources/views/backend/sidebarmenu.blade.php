@canany([
    'notification.create',
    'notification.send',
    'notification.history'
])
<li><a href="/admin/push-notification" class="nav-item @if(isset($menuParent) && $menuParent == 'notification') active @endif" ><i class="icon" data-feather="bell"></i>Push Notification</a></li>
@endcanany