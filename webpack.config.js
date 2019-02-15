const path = require("path");

module.exports = {
    entry: {
        main: "./src/ts/main.ts",
    },
    module: {
        rules: [
            {
                test: "/\.tsx?$/",
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
    },
};