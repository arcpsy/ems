import os

NO_DESCEND_DIRS = {'.git'}
EXCLUDE_FILES = {'README', 'README.md', 'readme.txt', 'project_tree.py'}

def print_tree(path, prefix=""):
    try:
        contents = sorted(os.listdir(path))
    except PermissionError:
        return

    # Filter files to exclude
    contents = [
        item for item in contents
        if item not in EXCLUDE_FILES
    ]

    pointers = ['├── '] * (len(contents) - 1) + ['└── ']

    for pointer, item in zip(pointers, contents):
        item_path = os.path.join(path, item)
        if os.path.isdir(item_path):
            print(f"{prefix}{pointer}{item}/")
            if item not in NO_DESCEND_DIRS:
                extension = '│   ' if pointer == '├── ' else '    '
                print_tree(item_path, prefix + extension)
        else:
            print(f"{prefix}{pointer}{item}")

if __name__ == "__main__":
    root = os.path.dirname(os.path.abspath(__file__))
    project_name = os.path.basename(root)
    print(f"{project_name}/")
    print_tree(root)
