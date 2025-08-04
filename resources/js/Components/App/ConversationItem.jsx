import { Link, usePage } from "@inertiajs/react";
import UserAvatar from "./UserAvatar";
import GroupAvatar from "./GroupAvatar";
import UserOptionsDropdown from "./UserOptionsDropdown";

export default function ConversationItem({
    conversation,
    selectedConversation = null,
    online = null,
}) {
    const page = usePage();
    const currentUser = page.props.auth.user;

    let isSelected =
        selectedConversation &&
        selectedConversation.id === conversation.id &&
        selectedConversation.is_group === conversation.is_group;

    let borderClass = isSelected
        ? "border-l-4 border-blue-500 bg-black/20"
        : "border-l-4 border-transparent";

    return (
        <Link
            href={
                conversation.is_group
                    ? route("chat.group", conversation)
                    : route("chat.user", conversation)
            }
            preserveState
            className={`conversation-item flex items-center gap-2 p-2 text-gray-300 transition-all cursor-pointer hover:bg-black/30 ${borderClass} ${
                conversation.is_user && currentUser.is_admin ? "pr-2" : "pr-4"
            }`}
        >
            {/* Avatar */}
            {conversation.is_user && (
                <UserAvatar user={conversation} online={online} />
            )}
            {conversation.is_group && <GroupAvatar />}

            {/* Info */}
            <div
                className={`flex-1 text-xs max-w-full overflow-hidden ${
                    conversation.is_user && conversation.blocked_at
                        ? "opacity-50"
                        : ""
                }`}
            >
                <div className="flex justify-between items-center gap-1">
                    <h3 className="text-sm font-semibold truncate">
                        {conversation.name}
                    </h3>
                    {conversation.last_message_date && (
                        <span className="text-nowrap text-xs text-gray-400">
                            {conversation.last_message_date}
                        </span>
                    )}
                </div>
                {conversation.last_message && (
                    <p className="text-xs text-gray-400 truncate">
                        {conversation.last_message}
                    </p>
                )}
            </div>

            {/* Admin Options */}
            {currentUser.is_admin && conversation.is_user && (
                <UserOptionsDropdown conversation={conversation} />
            )}
        </Link>
    );
}
