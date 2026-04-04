export type User = {
    id: number;
    name: string;
    email: string;
    role?: string;
    avatar?: string;
    status?: string;
    email_verified_at?: string | null;
    created_at?: string;
    updated_at?: string;
    [key: string]: unknown;
};

export type Auth = {
    user: User;
    role: string | null;
    permissions: string[];
    roles: string[];
};

export type TwoFactorConfigContent = {
    title: string;
    description: string;
    buttonText: string;
};
