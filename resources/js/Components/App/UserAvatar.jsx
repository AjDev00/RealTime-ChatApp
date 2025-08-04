export default function UserAvatar({ user, online = null, profile = false }) {
    const sizeClass = profile ? "w-40 h-40 text-5xl" : "w-10 h-10 text-base";

    return (
        <div className="relative inline-block">
            <div
                className={`rounded-full ${sizeClass} bg-gray-400 text-gray-800 flex items-center justify-center overflow-hidden`}
            >
                {user.avatar_url ? (
                    <img
                        src={user.avatar_url}
                        alt="User Avatar"
                        className="w-full h-full object-cover"
                    />
                ) : (
                    <span className="font-semibold uppercase">
                        {user.name?.charAt(0)}
                    </span>
                )}
            </div>

            {online !== null && (
                <span
                    className={`absolute -top-0 -right-0 w-3 h-3 rounded-full border-2 border-white ${
                        online ? "bg-green-500" : "bg-gray-400"
                    }`}
                ></span>
            )}
        </div>
    );
}
