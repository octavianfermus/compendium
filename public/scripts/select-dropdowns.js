$(document).ready(function() {
    var supportedModes = [
        "ABAP",
        "ABC",
        "ActionScript",
        "ADA",
        "Apache_Conf",
        "AsciiDoc",
        "Assembly_x86",
        "AutoHotKey",
        "BatchFile",
        "C_Cpp",
        "C9Search",
        "Cirru",
        "Clojure",
        "Cobol",
        "coffee",
        "ColdFusion",
        "CSharp",
        "CSS",
        "Curly",
        "D",
        "Dart",
        "Diff",
        "Dockerfile",
        "Dot",
        "Dummy",
        "DummySyntax",
        "Eiffel",
        "EJS",
        "Elixir",
        "Elm",
        "Erlang",
        "Forth",
        "FTL",
        "Gcode",
        "Gherkin",
        "Gitignore",
        "Glsl",
        "Gobstones",
        "golang",
        "Groovy",
        "HAML",
        "Handlebars",
        "Haskell",
        "haXe",
        "HTML",
        "HTML_Elixir",
        "HTML_Ruby",
        "INI",
        "Io",
        "Jack",
        "Jade",
        "Java",
        "JavaScript",
        "JSON",
        "JSONiq",
        "JSP",
        "JSX",
        "Julia",
        "LaTeX",
        "Lean",
        "LESS",
        "Liquid",
        "Lisp",
        "LiveScript",
        "LogiQL",
        "LSL",
        "Lua",
        "LuaPage",
        "Lucene",
        "Makefile",
        "Markdown",
        "Mask",
        "MATLAB",
        "Maze",
        "MEL",
        "MUSHCode",
        "MySQL",
        "Nix",
        "NSIS",
        "ObjectiveC",
        "OCaml",
        "Pascal",
        "Perl",
        "pgSQL",
        "PHP",
        "Powershell",
        "Praat",
        "Prolog",
        "Properties",
        "Protobuf",
        "Python",
        "R",
        "Razor",
        "RDoc",
        "RHTML",
        "RST",
        "Ruby",
        "Rust",
        "SASS",
        "SCAD",
        "Scala",
        "Scheme",
        "SCSS",
        "SH",
        "SJS",
        "Smarty",
        "snippets",
        "Soy_Template",
        "Space",
        "SQL",
        "SQLServer",
        "Stylus",
        "SVG",
        "Swift",
        "Tcl",
        "Tex",
        "Text",
        "Textile",
        "Toml",
        "Twig",
        "Typescript",
        "Vala",
        "VBScript",
        "Velocity",
        "Verilog",
        "VHDL",
        "Wollok",
        "XML",
        "XQuery",
        "YAML",
        "Django"
    ];
    $("input[name='language']").autocomplete({
      source: supportedModes
    });
});