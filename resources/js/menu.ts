import {
    BarChart3,
    BookOpen,
    DollarSign,
    FolderGit2,
    LayoutDashboard,
    FileText,
    Shield,
} from 'lucide-vue-next';
import type { MenuItem, NavItem } from '@/types';

export const mainNavItems: MenuItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutDashboard,
        permission: 'view dashboard',
    },
    {
        title: 'Transactions',
        href: '#',
        icon: DollarSign,
        permission: 'view transactions',
        items: [
            {
                title: 'All Transactions',
                href: '/transaction-records',
                icon: FileText,
                permission: 'view transactions',
            },
            {
                title: 'Analytics',
                href: '/transaction-analytics',
                icon: BarChart3,
                permission: 'view transactions',
            },
        ],
    },
    {
        title: 'Users',
        href: '#',
        icon: BookOpen,
        permission: 'view users',
        items: [
            {
                title: 'All Users',
                href: '/users',
                icon: FileText,
                permission: 'view users',
            },
            {
                title: 'Role',
                href: '/roles',
                icon: FolderGit2,
                permission: 'view roles',
            },
            {
                title: 'Permissions',
                href: '/permissions',
                icon: Shield,
                permission: 'view permissions',
            },
            {
                title: 'Role & Permission',
                href: '/role-permissions',
                icon: Shield,
                permission: 'view roles',
            },
        ],
    },
];

export const footerNavItems: NavItem[] = [
    {
        title: 'Front Website',
        href: '#',
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits',
        icon: BookOpen,
    },
];
