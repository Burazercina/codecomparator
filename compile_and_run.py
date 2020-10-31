import sys
import os
from os import listdir
from os.path import isfile, join
import string
import random
import subprocess

def rand_string(size=6, chars=string.ascii_uppercase + string.digits):
    return ''.join(random.choice(chars) for _ in range(size))
def main():
    source_path = sys.argv[1]
    inputs_folder = sys.argv[2]
    output_path = sys.argv[3]
    comments_path = sys.argv[4]
    time_limit = sys.argv[5]
    
    exe_name = rand_string(10)
    compile_command = 'g++ ' + '"' + str(source_path) + '"' + ' ' + '-o ' + exe_name
    os.system(compile_command)

    input_files = [f for f in listdir(inputs_folder) if isfile(join(inputs_folder, f))]
    for input_file in input_files:
        output_file_name = os.path.splitext(input_file)[0] + '.out'
        f = open(join(inputs_folder,input_file), "r")
        input_text = f.read()
        f.close()
        try:
            completed = subprocess.run(exe_name, input=input_text, text=True, timeout=int(time_limit), capture_output=True)

            output = completed.stdout
            errors = completed.stderr
            f = open(join(output_path,output_file_name), "w+")
            f.write(output)
            f.close()
        except subprocess.TimeoutExpired as e:
            output = e.stdout
            f = open(join(output_path,output_file_name), "w+")
            f.write(output)
            f.close()

            f = open(join(comments_path,output_file_name), "w+")
            f.write('TLE')
            f.close()
    os.remove(exe_name + '.exe')
main()