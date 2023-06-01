#!/usr/local/munkireport/munkireport-python3
# Applications for munkireport
# By Tuxudo

import subprocess
import os
import plistlib
import sys

def get_app_bundle_version(app_path):
    '''Return the CFBundleVersion of the app based on its path'''

    try:
        with open(app_path+"/Contents/Info.plist", 'rb') as fp:
            info_plist = plistlib.load(fp)
        return info_plist['CFBundleVersion']
    except Exception:
        return ""

def get_app_version(app_path):
    '''Return the CFBundleShortVersionString of the app based on its path'''

    try:
        with open(app_path+"/Contents/Info.plist", 'rb') as fp:
            info_plist = plistlib.load(fp)
        return info_plist['CFBundleShortVersionString']
    except Exception:
        return ""

def get_applications_info():
    '''Uses system profiler to get applications for this machine.'''
    cmd = ['/usr/sbin/system_profiler', 'SPApplicationsDataType', '-xml']
    proc = subprocess.Popen(cmd, shell=False, bufsize=-1,
                            stdin=subprocess.PIPE,
                            stdout=subprocess.PIPE, stderr=subprocess.PIPE)
    (output, unused_error) = proc.communicate()

    try:
        try:
            plist = plistlib.readPlistFromString(output)
        except AttributeError as e:
            plist = plistlib.loads(output)
        # system_profiler xml is an array
        sp_dict = plist[0]
        items = sp_dict['_items']
        return items
    except Exception:
        return {}

def flatten_applications_info(array):
    '''Un-nest applications, return array with objects with relevant keys'''
    out = []
    for obj in array:
        app = {'has64bit': 0}
        for item in obj:
            if item == '_items':
                out = out + flatten_applications_info(obj['_items'])
            elif item == '_name':
                app['name'] = obj[item]
            elif item == 'lastModified':
                app['last_modified'] = obj[item]
            elif item == 'obtained_from':
                app['obtained_from'] = obj[item]
            elif item == 'path':
                app['path'] = obj[item]
                app['bundle_version'] = get_app_bundle_version(obj[item])
            elif item == 'runtime_environment':
                app['runtime_environment'] = obj[item]
            elif item == 'version':
                app['version'] = obj[item]
            elif item == 'info':
                app['info'] = obj[item]
            elif item == 'signed_by':
                app['signed_by'] = obj[item][0]
            elif item == 'has64BitIntelCode' and obj[item] == 'yes':
                app['has64bit'] = 1
            elif item == 'arch_kind':
                app['runtime_environment'] = obj[item]
                if (obj[item] == 'arch_i64' or obj[item] == 'arch_i32_i64' or obj[item] == 'arch_arm_i64' or obj[item] == 'arch_ios'):
                    app['has64bit'] = 1

        # Sometimes Spotlight doesn't properly get the app version, get that manually
        if "version" not in app and "path" in app:
            app['version'] = get_app_version(app['path'])

        out.append(app)
    return out

def main():
    """Main"""

    # Get results
    result = dict()
    info = get_applications_info()
    result = flatten_applications_info(info)

    # Write applications results to cache
    cachedir = '%s/cache' % os.path.dirname(os.path.realpath(__file__))
    output_plist = os.path.join(cachedir, 'applications.plist')
    try:
        plistlib.writePlist(result, output_plist)
    except:
        with open(output_plist, 'wb') as fp:
            plistlib.dump(result, fp, fmt=plistlib.FMT_XML)
    #print plistlib.writePlistToString(result)


if __name__ == "__main__":
    main()
