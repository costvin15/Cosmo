const path = require("path");

module.exports = {
    entry: {
        main: "./src/ts/main.ts",
        register: "./src/ts/pages/register/script.ts",
        login: "./src/ts/pages/login/script.ts",
        profile: "./src/ts/pages/profile/script.ts",
        history: "./src/ts/pages/history/script.ts",
        problems: "./src/ts/pages/problems/script.ts",
        pvp: "./src/ts/pages/pvp/script.ts",
        administrator_group_activities: "./src/ts/pages/administrator/groupactivities/script.ts",
        administrator_activities: "./src/ts/pages/administrator/activities/script.ts",
        administrator_users: "./src/ts/pages/administrator/users/script.ts",
        administrator_classes: "./src/ts/pages/administrator/classes/script.ts",
        administrator_classes_view: "./src/ts/pages/administrator/classes/view/script.ts",
        administrator_classes_challenges: "./src/ts/pages/administrator/classes/challenges/script.ts"
    },
    module: {
        rules: [
            {
                test: /\.tsx?$/,
                use: "ts-loader",
                exclude: "/node_modules/"
            },
        ]
    },
    resolve: {
        extensions: [".tsx", ".ts", ".js"]
    },
    output: {
        path: path.resolve(__dirname, "public", "dist", "js"),
    }
};