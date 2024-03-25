export interface fetchJSONOptions {
    method?: string;
    body?: Object;
    headers?: Object;
    showSuccess?: boolean;
    showError?: boolean;
}

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string;
    created_at: string;
    updated_at: string;
}

export interface Message {
    id: number;
    room_id: number;
    user_id: number;
    user: User;
    content: string;
    deleted_at: string;
    created_at: string;
    updated_at: string;
}

export interface Room {
    id: number;
    user_id: number;
    user: User;
    messages: Message[];
    messages_count: number;
    title: string;
    description: string;
    created_at: string;
    updated_at: string;
}
