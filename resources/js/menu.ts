import { BookOpen, FolderGit2, LayoutDashboard, FileText, Shield } from 'lucide-vue-next';
import type {MenuItem, NavItem } from '@/types';

export const mainNavItems: MenuItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutDashboard,
    },
    {
        title: 'Users',
        href: '#',
        icon: BookOpen,
        items: [
            {
                title: 'All Users',
                href: '/users',
                icon: FileText,
            },
            {
                title: 'Role',
                href: '/roles',
                icon: FolderGit2,
            },
            {
                title: 'Permissions',
                href: '/permissions',
                icon: Shield,
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
