git commands

# Fetchs out all remote branches so you see them
git fetch origin
git branch -r
git branch -l

# Will checkout and switch to remote branch person
# I know if there is no local person branch then this works as expected
git checkout person

# You can switch back and forth with
git checkout master
git branch -l

# Pull with
git pull origin master
git pull origin person

