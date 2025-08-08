import ChatLayout from "@/Layouts/ChatLayout";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { useEffect, useRef, useState } from "react";
import { ChatBubbleLeftRightIcon } from "@heroicons/react/24/solid";
import ConversationHeader from "@/Components/App/ConversationHeader";
import MessageItem from "@/Components/App/MessageItem";
import MessageInput from "@/Components/App/MessageInput";
// import { useEventBus } from "@/EventBus";
import { usePage } from "@inertiajs/react";

export default function Home({ selectedConversation = null, messages = null }) {
    const [localMessages, setLocalMessages] = useState([]);
    const messagessCtrRef = useRef(null);
    const page = usePage();
    const currentUser = page.props.auth.user;

    useEffect(() => {
        if (!selectedConversation || !currentUser) return;

        // Build the channel name exactly like backend does
        let channel = `message.group.${selectedConversation.id}`;
        if (selectedConversation.is_user) {
            channel = `message.user.${[
                parseInt(currentUser.id, 10),
                parseInt(selectedConversation.id, 10),
            ]
                .sort((a, b) => a - b)
                .join("-")}`;
        }
        console.log("Subscribing to channel:", channel);

        // Subscribe to private channel and listen for the broadcastAs()
        const echoChannel = Echo.private(channel)
            .error((err) => {
                console.error("Echo channel error:", err);
            })
            .listen(".SocketMessage", (eventPayload) => {
                console.log("SocketMessage received (active):", eventPayload);
                const msg = eventPayload.message;

                // Append message to local messages state if it belongs to this conversation
                setLocalMessages((prev) => {
                    // Prevent duplicates by id if needed
                    if (prev.some((m) => m.id === msg.id)) return prev;
                    return [...prev, msg];
                });
            });

        // Cleanup: leave private channel properly when selectedConversation changes
        return () => {
            try {
                Echo.leave(`private-${channel}`);
            } catch (err) {
                // fallback
                try {
                    Echo.leave(channel);
                } catch (e) {
                    console.warn("Failed to leave channel:", channel, e);
                }
            }
        };
    }, [selectedConversation, currentUser]);
    // const { on } = useEventBus();

    // const messageCreated = (message) => {
    //     if (
    //         selectedConversation &&
    //         selectedConversation.is_group &&
    //         selectedConversation.id == message.group_id
    //     ) {
    //         setLocalMessages((prevMessages) => [...prevMessages, message]);
    //     }
    //     if (
    //         selectedConversation &&
    //         selectedConversation.is_user &&
    //         (selectedConversation.id == message.sender_id ||
    //             selectedConversation.id == message.receiver_id)
    //     ) {
    //         setLocalMessages((prevMessages) => [...prevMessages, message]);
    //     }
    // };

    useEffect(() => {
        setTimeout(() => {
            if (messagessCtrRef.current) {
                messagessCtrRef.current.scrollTop =
                    messagessCtrRef.current.scrollHeight;
            }
        }, 10);

        // const offCreated = on("message.created", messageCreated);

        // return () => {
        //     offCreated();
        // };
    }, [selectedConversation]);

    useEffect(() => {
        setLocalMessages(messages ? messages.data.reverse() : []);
    }, [messages]);
    return (
        <>
            {!messages && (
                <div className="flex flex-col gap-8 justify-center items-center text-center h-full opacity-35">
                    <div className="text-2xl md:text-4xl p-16 text-slate-200">
                        Please select conversation to see messages
                    </div>
                    <ChatBubbleLeftRightIcon className="w-32 h-32 inline-block" />
                </div>
            )}
            {messages && (
                <>
                    <ConversationHeader
                        selectedConversation={selectedConversation}
                    />
                    <div
                        ref={messagessCtrRef}
                        className="flex-1 overflow-y-auto p-5"
                    >
                        {/* Messages */}

                        {localMessages.length === 0 && (
                            <div className="flex justify-center items-center h-full">
                                <div className="text-lg text-slate-200">
                                    No messages found
                                </div>
                            </div>
                        )}
                        {localMessages.length > 0 && (
                            <div className="flex-1 flex flex-col">
                                {localMessages.map((message) => (
                                    <MessageItem
                                        key={message.id}
                                        message={message}
                                    />
                                ))}
                            </div>
                        )}
                    </div>
                    <MessageInput conversation={selectedConversation} />
                </>
            )}
        </>
    );
}

Home.layout = (page) => {
    return (
        <AuthenticatedLayout user={page.props.auth.user} children={page}>
            <ChatLayout children={page} />
        </AuthenticatedLayout>
    );
};
