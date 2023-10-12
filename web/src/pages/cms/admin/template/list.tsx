import { useCustomMutation } from '@refinedev/core'
import { Main, Modal, StatusEmpty, useTable } from '@duxweb/dux-refine'
import { Card, Tag, Button, Image, Tooltip, Popconfirm } from 'tdesign-react/esm'
import { Icon } from 'tdesign-icons-react'

const List = () => {
  const { data, refetch } = useTable({})

  return (
    <Main>
      <div className='mb-4'>
        {data && data.length > 0 ? (
          <div className='grid grid-cols-2 gap-4 2xl:grid-cols-4 xl:grid-cols-3'>
            {data?.map((item, key) => (
              <CardItem key={key} item={item} refetch={refetch} />
            ))}
          </div>
        ) : (
          <Card>
            <StatusEmpty />
          </Card>
        )}
      </div>
    </Main>
  )
}

interface CardItemProps {
  item?: Record<string, any>
  refetch?: () => void
}

const CardItem = ({ item, refetch }: CardItemProps) => {
  const { mutate } = useCustomMutation()
  return (
    <Card
      title={item?.name}
      actions={<Tag theme='success'>默认</Tag>}
      bordered
      cover={<Image src={item?.image} className='h-50' />}
      footer={
        <div className='grid grid-cols-3 items-center justify-between divide-x divide-gray-200'>
          <div className='flex justify-center'>
            <Modal
              title='主题说明'
              trigger={
                <Button variant='text'>
                  <Tooltip content='主题说明'>
                    <Icon name='help-circle' />
                  </Tooltip>
                </Button>
              }
            >
              <div className='p-4'>{item?.help || '暂无'}</div>
            </Modal>
          </div>
          <div className='flex justify-center'>
            <Popconfirm
              content='确认切换吗'
              destroyOnClose
              placement='top'
              showArrow
              theme='default'
              onConfirm={() => {
                mutate({
                  url: `cms/template/${item?.id}`,
                  method: 'patch',
                  values: {},
                  successNotification: () => {
                    refetch?.()
                    return false
                  },
                })
              }}
            >
              <Button variant='text'>
                <Tooltip content='切换'>
                  <Icon name='component-switch' />
                </Tooltip>
              </Button>
            </Popconfirm>
          </div>
          <div className='flex justify-center'>
            <Modal
              title='主题配置'
              width={640}
              trigger={
                <Button variant='text'>
                  <Tooltip content='配置'>
                    <Icon name='edit-1' />
                  </Tooltip>
                </Button>
              }
              component={() => import('./save')}
              componentProps={{ id: item?.id }}
            />
          </div>
        </div>
      }
    ></Card>
  )
}

export default List
