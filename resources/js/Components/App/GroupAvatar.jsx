import { UsersIcon } from "@heroicons/react/24/solid";

export default function GroupAvatar({ profile = false }) {
    const sizeClass = profile ? "w-40 h-40 text-5xl" : "w-10 h-10 text-base";

    return (
        <div className={`relative inline-block`}>
            <div
                className={`bg-gray-400 text-gray-800 rounded-full ${sizeClass} flex items-center justify-center`}
            >
                <UsersIcon className="w-5 h-5" />
            </div>
        </div>
    );
}
