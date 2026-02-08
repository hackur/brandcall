import { useEffect } from 'react';
import { usePage } from '@inertiajs/react';
import { toast, Toaster } from 'sonner';

/**
 * Flash message handler that converts Laravel flash messages to toast notifications.
 * 
 * Listens for 'success', 'error', 'warning', and 'info' flash messages
 * from Laravel's session and displays them as Sonner toasts.
 */
export function FlashMessages() {
    const { flash } = usePage().props as any;

    useEffect(() => {
        if (flash?.success) {
            toast.success(flash.success);
        }
        if (flash?.error) {
            toast.error(flash.error);
        }
        if (flash?.warning) {
            toast.warning(flash.warning);
        }
        if (flash?.info) {
            toast.info(flash.info);
        }
    }, [flash]);

    return null;
}

/**
 * Toast provider component. Place this once in your app layout.
 * Renders the Toaster container and the flash message listener.
 */
export function ToastProvider() {
    return (
        <>
            <Toaster
                position="top-right"
                toastOptions={{
                    duration: 4000,
                    className: 'text-sm',
                }}
                richColors
                closeButton
            />
            <FlashMessages />
        </>
    );
}
